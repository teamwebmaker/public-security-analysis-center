<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Services\Incidents\IncidentParticipantOptions;
use App\Services\Orders\OrderBranchAccess;
use App\Services\Orders\OrderManager;
use App\Services\Sms\OrderSmsNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        private OrderManager $orderManager,
        private OrderBranchAccess $branchAccess,
        private IncidentParticipantOptions $participantOptions,
        private OrderSmsNotifier $smsNotifier
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Order::class);
        $user = $request->user();

        $orders = Order::query()
            ->visibleTo($user)
            ->with([
                'branch.company',
                'creator:id,full_name',
                'userParticipants.user.role',
                'publicShare',
            ])
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view($user->isAdmin() ? 'admin.orders.index' : 'management.orders.index', [
            'orders' => $orders,
            'sidebarItems' => $user->isAdmin() ? null : $this->managementSidebar($user->getRoleName()),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Order::class);
        $branches = $this->branchAccess->forUser($request->user());

        return view($request->user()->isAdmin() ? 'admin.orders.create' : 'management.orders.create', [
            'branches' => $branches,
            'participantOptions' => $this->participantOptions->forBranches($branches),
            'sidebarItems' => $request->user()->isAdmin()
                ? null
                : $this->managementSidebar($request->user()->getRoleName()),
        ]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        try {
            $result = $this->orderManager->create($request->validated(), $request->user());
            $this->smsNotifier->notifyCreated($result['order'], $result['new_participants']);
        } catch (Throwable $e) {
            Log::error('Order creation failed', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'order' => 'ბრძანების შექმნა ვერ მოხერხდა. გთხოვთ, სცადოთ თავიდან.',
            ]);
        }

        $route = $request->user()->isAdmin() ? 'orders.show' : 'management.orders.show';

        return redirect()->route($route, $result['order'])
            ->with('success', 'ბრძანება წარმატებით შეიქმნა.');
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorize('view', $order);
        $order->load([
            'branch.company',
            'creator:id,full_name',
            'userParticipants.user.role',
            'publicShare',
        ]);

        return view($request->user()->isAdmin() ? 'admin.orders.show' : 'management.orders.show', [
            'order' => $order,
            'sidebarItems' => $request->user()->isAdmin()
                ? null
                : $this->managementSidebar($request->user()->getRoleName()),
        ]);
    }

    public function edit(Request $request, Order $order): View
    {
        $this->authorize('update', $order);
        $order->load(['branch.company', 'userParticipants.user.role']);
        $branches = collect([$order->branch]);
        $participantOptions = $this->participantOptions->forBranches($branches);
        $knownIds = collect($participantOptions[$order->branch_id] ?? [])->pluck('id');

        foreach ($order->userParticipants as $participant) {
            if ($knownIds->contains($participant->user_id)) {
                continue;
            }

            $participantOptions[$order->branch_id][] = [
                'id' => $participant->user_id,
                'name' => $participant->user->full_name,
                'role' => $participant->user->getRoleName(),
                'role_label' => $participant->user->getRoleName() === 'company_leader'
                    ? 'კომპანიის ხელმძღვანელი'
                    : 'პასუხისმგებელი პირი',
            ];
        }

        return view($request->user()->isAdmin() ? 'admin.orders.edit' : 'management.orders.edit', [
            'order' => $order,
            'branches' => $branches,
            'participantOptions' => $participantOptions,
            'sidebarItems' => $request->user()->isAdmin()
                ? null
                : $this->managementSidebar($request->user()->getRoleName()),
        ]);
    }

    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        try {
            $result = $this->orderManager->update($order, $request->validated(), $request->user());

            if ($result['new_participants']->isNotEmpty()) {
                $this->smsNotifier->notifyCreated($result['order'], $result['new_participants']);
            }
        } catch (Throwable $e) {
            Log::error('Order update failed', [
                'order_id' => $order->id,
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'order' => 'ბრძანების განახლება ვერ მოხერხდა. გთხოვთ, სცადოთ თავიდან.',
            ]);
        }

        $route = $request->user()->isAdmin() ? 'orders.show' : 'management.orders.show';

        return redirect()->route($route, $result['order'])
            ->with('success', 'ბრძანება წარმატებით განახლდა.');
    }

    public function document(Order $order): BinaryFileResponse
    {
        $this->authorize('view', $order);
        abort_unless(Storage::disk('local')->exists($order->document_path), 404);

        $response = response()->file(
            Storage::disk('local')->path($order->document_path),
            [
                'Content-Type' => $order->document_mime_type ?: 'application/octet-stream',
                'Cache-Control' => 'private, no-store',
            ]
        );
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $order->document_original_name
        );

        return $response;
    }

    public function downloadDocument(Order $order): BinaryFileResponse
    {
        $this->authorize('view', $order);
        abort_unless(Storage::disk('local')->exists($order->document_path), 404);

        return response()->download(
            Storage::disk('local')->path($order->document_path),
            $order->document_original_name,
            [
                'Content-Type' => $order->document_mime_type ?: 'application/octet-stream',
                'Cache-Control' => 'private, no-store',
            ]
        );
    }

    public function sign(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('sign', $order);

        $order->userParticipants()
            ->where('user_id', $request->user()->id)
            ->whereNull('signed_at')
            ->update(['signed_at' => now(), 'updated_at' => now()]);

        return back()->with('success', 'ბრძანების ხელმოწერა დადასტურებულია.');
    }

    private function managementSidebar(string $role): array
    {
        return config('sidebar.' . str_replace('_', '-', $role), []);
    }
}
