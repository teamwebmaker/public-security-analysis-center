<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use App\Services\Messages\MessageStoreService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class MessagesController extends Controller
{
    protected string $resourceName = 'messages';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Supports both legacy '?42' and standard '?message=42' notification links.
        $query = request()->query();
        $selectedMessageId = null;

        if (!empty($query)) {
            if (isset($query['message']) && is_numeric($query['message'])) {
                $selectedMessageId = (int) $query['message'];
            } else {
                $firstKey = (string) array_key_first($query);
                if (is_numeric($firstKey)) {
                    $selectedMessageId = (int) $firstKey;
                }
            }
        }

        $messagesQuery = $this->filteredMessagesQuery($request);
        $selectedMessage = $selectedMessageId ? Message::query()->find($selectedMessageId) : null;

        if ($selectedMessage && $selectedMessage->read_at === null) {
            $selectedMessage->forceFill(['read_at' => now()])->save();
        }

        return view('admin.messages.index', [
            'messages' => $messagesQuery->latest()->paginate(12)->withQueryString(),
            'selectedMessage' => $selectedMessage,
            'matchingMessageCount' => (clone $messagesQuery)->count(),
            'matchingUnreadCount' => (clone $messagesQuery)->whereNull('read_at')->count(),
            'resourceName' => $this->resourceName,
            'filters' => Schema::hasColumn('messages', 'source') ? [
                'source' => [
                    'label' => 'წყარო',
                    'options' => [
                        'system' => 'სისტემური',
                        'payment' => 'გადახდა',
                        'user' => 'მომხმარებელი',
                    ],
                ],
            ] : [],
        ]);
    }

    public function store(StoreMessageRequest $request, MessageStoreService $messageStoreService)
    {
        $validated = $request->validated();
        $locale = app()->getLocale();
        $validated['service_ids'] = $validated['service_ids'] ?? [];

        // Error on service request with no service chosen 
        if ($validated['subject'] === 'Request to Service' && empty($validated['service_ids'])) {
            return redirect()
                ->back()
                ->withErrors(['service_ids' => $locale == 'en' ? 'Please select at least one service.' : 'გთხოვთ აირჩიოთ მინიმუმ ერთი სერვისი'])
                ->withInput();
        }


        $messageStoreService->createAndDispatch($validated);

        $req_message = $locale == 'en' ? 'Your message has been sent.' : 'შეტყობინება გაიგზავნა წარმატებით.';
        return redirect()->back()->with('success', $req_message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        $message->delete();
        return redirect()->route('messages.index')->with('success', 'შეტყობინება წარმატებით წაიშალა.');
    }

    public function markRead(Message $message)
    {
        if (!$message->read_at) {
            $message->forceFill(['read_at' => now()])->save();
        }

        return redirect()->route('messages.index')->with('success', 'შეტყობინება მონიშნულია წაკითხულად.');
    }

    public function markAllRead(Request $request)
    {
        $updated = $this->filteredMessagesQuery($request)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', $updated > 0
            ? "წაკითხულად მოინიშნა {$updated} შეტყობინება."
            : 'წასაკითხი შეტყობინება არ მოიძებნა.');
    }

    private function filteredMessagesQuery(Request $request): Builder
    {
        $query = Message::query();
        $hasSourceColumn = Schema::hasColumn('messages', 'source');
        $hasTypeColumn = Schema::hasColumn('messages', 'type');
        $source = (string) $request->input('filter.source', '');
        $readStatus = (string) $request->input('filter.read_status', '');
        $search = trim((string) $request->input('filter.search', ''));

        if ($hasSourceColumn && in_array($source, ['system', 'payment', 'user'], true)) {
            if ($source === 'payment' && $hasTypeColumn) {
                $query->where('source', 'system')->where('type', 'payment');
            } elseif ($source === 'system' && $hasTypeColumn) {
                $query->where('source', 'system')->where('type', '!=', 'payment');
            } else {
                $query->where('source', $source);
            }
        }

        if ($readStatus === 'unread') {
            $query->whereNull('read_at');
        } elseif ($readStatus === 'read') {
            $query->whereNotNull('read_at');
        }

        if ($search !== '') {
            $like = "%{$search}%";
            $query->where(function (Builder $query) use ($like) {
                $query->where('subject', 'LIKE', $like)
                    ->orWhere('message', 'LIKE', $like)
                    ->orWhere('full_name', 'LIKE', $like)
                    ->orWhere('phone', 'LIKE', $like)
                    ->orWhere('email', 'LIKE', $like);
            });
        }

        return $query;
    }
}
