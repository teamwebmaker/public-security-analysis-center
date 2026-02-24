<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use App\Services\Messages\MessageStoreService;
use Illuminate\Support\Facades\Schema;

class MessagesController extends Controller
{
    protected string $resourceName = 'messages';
    /**
     * Display a listing of the resource.
     */
    public function index()
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

        $hasSourceColumn = Schema::hasColumn('messages', 'source');
        $sourceFilter = request('filter.source');
        $allowedSources = ['system', 'user'];
        $sourceFilter = in_array($sourceFilter, $allowedSources, true) ? $sourceFilter : null;

        $messagesQuery = Message::query()->orderBy('created_at', 'DESC');

        if ($hasSourceColumn && $sourceFilter) {
            $messagesQuery->where('source', $sourceFilter);
        }

        return view('admin.messages.index', [
            'messages' => $messagesQuery->paginate(6),
            'selectedMessageId' => $selectedMessageId,
            'resourceName' => $this->resourceName,
            'filters' => $hasSourceColumn ? [
                'source' => [
                    'label' => 'წყარო',
                    'options' => [
                        'system' => 'სისტემური (system)',
                        'user' => 'მომხმარებელი (user)',
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
}
