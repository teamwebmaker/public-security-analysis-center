@extends('layouts.admin.admin-dashboard')

@section('title', 'შეტყობინებები')

@section('styles')
    <style>
        .messages-inbox .inbox-filter-form {
            width: 100%;
        }

        .messages-inbox .message-list {
            max-height: 68dvh;
            overflow-y: auto;
        }

        .messages-inbox .message-detail {
            min-height: 420px;
        }

        @media (min-width: 1200px) {
            .messages-inbox .inbox-filter-form {
                width: auto;
            }
        }

        @media (max-width: 575.98px) {
            .messages-inbox .quick-filter {
                flex: 1 1 0;
            }

            .messages-inbox .mark-all-read-form,
            .messages-inbox .mark-all-read-form button,
            .messages-inbox .message-action,
            .messages-inbox .message-delete-form,
            .messages-inbox .message-delete-form button {
                width: 100%;
            }

            .messages-inbox .message-list {
                max-height: 52dvh;
            }

            .messages-inbox .message-detail {
                min-height: 0;
            }
        }
    </style>
@endsection

@section('main')
    @php
        $currentFilters = request('filter', []);
        $filterUrl = function (array $overrides = []) use ($currentFilters) {
            $filters = array_filter(array_merge($currentFilters, $overrides), fn ($value) => $value !== null && $value !== '');

            return route('messages.index', $filters ? ['filter' => $filters] : []);
        };
        $visual = function ($message) {
            if ($message->type === 'payment') {
            return ['icon' => 'bi-cash-coin', 'label' => '💰 გადახდა', 'class' => 'text-bg-success'];
            }

            if ($message->source === 'system') {
                return ['icon' => 'bi-cpu', 'label' => 'სისტემური', 'class' => 'text-bg-primary'];
            }

            return ['icon' => 'bi-person', 'label' => 'მომხმარებელი', 'class' => 'text-bg-secondary'];
        };
    @endphp

    @if(session()->has('success'))
        <x-ui.toast :messages="[session('success')]" type="success" />
    @endif

    <div class="card border-0 shadow-sm overflow-hidden messages-inbox">
        <div class="card-header bg-white border-bottom p-3 p-lg-4">
            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                <div>
                    <h1 class="h4 mb-1">შეტყობინებები</h1>
                    <p class="text-muted small mb-0">სულ: {{ $matchingMessageCount }} · წასაკითხი: {{ $matchingUnreadCount }}</p>
                </div>

                <form method="GET" action="{{ route('messages.index') }}" class="inbox-filter-form d-grid d-sm-flex gap-2">
                    <input type="search" name="filter[search]" value="{{ $currentFilters['search'] ?? '' }}"
                        class="form-control" placeholder="ძიება შეტყობინებებში..." aria-label="შეტყობინებების ძიება">
                    <select name="filter[source]" class="form-select" aria-label="შეტყობინების ტიპი">
                        <option value="">ყველა ტიპი</option>
                        <option value="system" @selected(($currentFilters['source'] ?? '') === 'system')>სისტემური</option>
                        <option value="payment" @selected(($currentFilters['source'] ?? '') === 'payment')>გადახდა</option>
                        <option value="user" @selected(($currentFilters['source'] ?? '') === 'user')>მომხმარებელი</option>
                    </select>
                    <select name="filter[read_status]" class="form-select" aria-label="წაკითხვის სტატუსი">
                        <option value="">ყველა სტატუსი</option>
                        <option value="unread" @selected(($currentFilters['read_status'] ?? '') === 'unread')>წასაკითხი</option>
                        <option value="read" @selected(($currentFilters['read_status'] ?? '') === 'read')>წაკითხული</option>
                    </select>
                    <button type="submit" class="btn btn-primary text-nowrap">ძიება</button>
                    <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary text-nowrap">გასუფთავება</a>
                </form>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                <a href="{{ $filterUrl(['read_status' => null]) }}" class="quick-filter btn btn-sm {{ empty($currentFilters['read_status']) ? 'btn-dark' : 'btn-outline-dark' }}">ყველა</a>
                <a href="{{ $filterUrl(['read_status' => 'unread']) }}" class="quick-filter btn btn-sm {{ ($currentFilters['read_status'] ?? '') === 'unread' ? 'btn-primary' : 'btn-outline-primary' }}">
                    წასაკითხი <span class="badge text-bg-light ms-1">{{ $matchingUnreadCount }}</span>
                </a>
                <a href="{{ $filterUrl(['read_status' => 'read']) }}" class="quick-filter btn btn-sm {{ ($currentFilters['read_status'] ?? '') === 'read' ? 'btn-secondary' : 'btn-outline-secondary' }}">წაკითხული</a>

                @if ($matchingUnreadCount > 0)
                    <form method="POST" action="{{ route('messages.mark-all-read', request()->query()) }}" class="mark-all-read-form ms-sm-auto">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-check2-all me-1"></i>ყველას წაკითხულად მონიშვნა
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row g-0">
            <aside id="messages-list" class="col-lg-5 border-end {{ $selectedMessage ? 'order-2 order-lg-1' : 'order-1' }}" aria-label="შეტყობინებების სია">
                <div class="message-list list-group list-group-flush">
                    @forelse($messages as $message)
                        @php
                            $type = $visual($message);
                            $linkQuery = request()->query();
                            $linkQuery['message'] = $message->id;
                            $isSelected = $selectedMessage?->id === $message->id;
                        @endphp
                        <a href="{{ route('messages.index', $linkQuery) }}"
                            class="list-group-item list-group-item-action p-3 {{ $isSelected ? 'active' : '' }} {{ $message->read_at === null && ! $isSelected ? 'bg-primary-subtle' : '' }}">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ $isSelected ? 'bg-white text-primary' : 'bg-light text-primary' }}" style="width: 38px; height: 38px;">
                                    <i class="bi {{ $type['icon'] }}"></i>
                                </div>
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <span class="badge {{ $isSelected ? 'text-bg-light text-dark' : $type['class'] }}">{{ $type['label'] }}</span>
                                        <small class="{{ $isSelected ? 'text-white-50' : 'text-muted' }} text-nowrap">{{ $message->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="fw-semibold text-truncate mt-2">{{ $message->subject ?: 'თემის გარეშე' }}</div>
                                    <div class="small text-truncate {{ $isSelected ? 'text-white-50' : 'text-muted' }} mt-1">{{ $message->message }}</div>
                                    @if ($message->read_at === null && ! $isSelected)
                                        <span class="d-inline-block rounded-circle bg-primary mt-2" style="width: 7px; height: 7px;" aria-label="წასაკითხი"></span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-4 text-center text-muted">შეტყობინებები ვერ მოიძებნა.</div>
                    @endforelse
                </div>
            </aside>

            <section class="col-lg-7 bg-light {{ $selectedMessage ? 'order-1 order-lg-2' : 'order-2' }}" aria-live="polite">
                @if ($selectedMessage)
                    @php($type = $visual($selectedMessage))
                    <article class="message-detail p-3 p-lg-4">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3 border-bottom pb-3 mb-3">
                            <div>
                                <a href="#messages-list" class="btn btn-sm btn-outline-secondary mb-3 d-lg-none">
                                    <i class="bi bi-arrow-left me-1"></i>შეტყობინებების სიაში დაბრუნება
                                </a>
                                <span class="badge {{ $type['class'] }} mb-2"><i class="bi {{ $type['icon'] }} me-1"></i>{{ $type['label'] }}</span>
                                <h2 class="h5 mb-1">{{ $selectedMessage->subject ?: 'თემის გარეშე' }}</h2>
                                <p class="small text-muted mb-0">{{ $selectedMessage->full_name }} · {{ $selectedMessage->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            <form method="POST" action="{{ route('messages.destroy', $selectedMessage) }}" class="message-delete-form" onsubmit="return confirm('ნამდვილად გსურთ შეტყობინების წაშლა?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>წაშლა</button>
                            </form>
                        </div>

                        @if ($selectedMessage->source === 'user')
                            <dl class="row small bg-white border rounded-3 p-3 mx-0 mb-4">
                                <dt class="col-sm-3 text-muted">ელ.ფოსტა</dt><dd class="col-sm-9">{{ $selectedMessage->email ?: '—' }}</dd>
                                <dt class="col-sm-3 text-muted">ტელეფონი</dt><dd class="col-sm-9 mb-0">{{ $selectedMessage->phone ?: '—' }}</dd>
                            </dl>
                        @endif

                        <div class="bg-white border rounded-3 p-3 p-lg-4" style="white-space: pre-line; overflow-wrap: anywhere;">{{ $selectedMessage->message }}</div>

                        @if ($selectedMessage->action_url)
                            <a href="{{ $selectedMessage->action_url }}" class="message-action btn btn-primary mt-4">
                                <i class="bi bi-box-arrow-up-right me-1"></i>{{ $selectedMessage->action_label ?: 'გახსნა' }}
                            </a>
                        @endif
                    </article>
                @else
                    <div class="message-detail d-flex flex-column align-items-center justify-content-center text-center text-muted p-5">
                        <i class="bi bi-envelope-open fs-1 mb-3"></i>
                        <h2 class="h6">აირჩიეთ შეტყობინება</h2>
                        <p class="small mb-0">სრული შინაარსი აქ გამოჩნდება.</p>
                    </div>
                @endif
            </section>
        </div>
    </div>

    <div class="pt-3">
        {{ $messages->links('pagination::bootstrap-5') }}
    </div>
@endsection
