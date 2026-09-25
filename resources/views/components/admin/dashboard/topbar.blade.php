<nav class="topbar d-flex align-items-center justify-content-between p-1  shadow-sm ">
    <button class="btn sidebar-toggler d-md-none" style="display: none;">
        <i class="bi bi-list fs-3 "></i>
    </button>

    <div class="topbar-inner d-flex align-items-center w-100 justify-content-between px-2 px-sm-4">
        <div class="d-flex align-items-center gap-2 gap-sm-4">
            <a href="{{ route('admin.dashboard.page') }}" class=" user-profile-btn text-decoration-none">
                <i class="bi bi-columns-gap fs-5" style="rotate: 90deg;"></i>
                <span class="d-none d-sm-block">მთავარი</span>
            </a>
            <a href="{{ route('guides.index') }}" class=" user-profile-btn text-decoration-none">
                <i class="bi bi-signpost-split fs-5"></i>
                <span class="d-none d-sm-block">გზამკვლევი</span>
            </a>
        </div>
        @php
            use App\Models\Message;
            $unreadMessagesCount = Message::whereNull('read_at')->count();
        @endphp

        <div class="d-flex align-items-center gap-1 gap-sm-2 flex-shrink-0">
            <div class="sms-balance d-flex align-items-center gap-1 border-end pe-1 pe-sm-2 me-0 me-sm-1 flex-shrink-0 text-nowrap" title="Sender.Ge SMS ბალანსი">
                <i class="bi bi-chat-dots-fill text-primary"></i>
                <span class="small fw-semibold text-muted d-none d-sm-inline">SMS:</span>
                <span id="sms-balance-value" class="sms-balance-value small fw-semibold text-muted" aria-live="polite">იტვირთება…</span>
                <button id="sms-balance-refresh" type="button" class="btn btn-sm btn-link text-secondary p-0" aria-label="SMS ბალანსის განახლება" title="განახლება">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>

            <!-- Messages -->
            <div class="position-relative">
                <x-ui.link-icon route="messages.index" icon="mailbox2-flag " />
                @if ($unreadMessagesCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" aria-label="{{ $unreadMessagesCount }} წასაკითხი შეტყობინება">
                        {{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}
                    </span>
                @endif
            </div>

            <!-- subscriptions  -->
            <x-ui.link-icon route="push.index" icon="bell-fill" />

            <!-- user avatar  -->
            <x-shared.user-avatar />
        </div>
    </div>
</nav>

@once
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const value = document.getElementById('sms-balance-value');
            const refreshButton = document.getElementById('sms-balance-refresh');

            if (!value || !refreshButton) {
                return;
            }

            const loadBalance = async () => {
                value.textContent = 'იტვირთება…';
                value.classList.remove('text-danger');
                value.classList.add('text-muted');
                refreshButton.disabled = true;

                try {
                    const response = await fetch(@json(route('sms.balance')), {
                        headers: {
                            Accept: 'application/json',
                        },
                    });
                    const data = await response.json();

                    if (!response.ok || !data.ok || typeof data.balance !== 'number') {
                        throw new Error('Balance request failed');
                    }

                    const balance = new Intl.NumberFormat('ka-GE', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    }).format(data.balance);
                    value.textContent = balance;
                    value.classList.remove('text-muted');
                    value.classList.add('text-success');
                } catch (error) {
                    value.textContent = 'მიუწვდომელია';
                    value.classList.remove('text-muted', 'text-success');
                    value.classList.add('text-danger');
                } finally {
                    refreshButton.disabled = false;
                }
            };

            refreshButton.addEventListener('click', loadBalance);
            loadBalance();
        });
    </script>
@endonce
