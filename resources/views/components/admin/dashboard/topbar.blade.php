<nav class="topbar d-flex align-items-center justify-content-between p-1  shadow-sm ">
    <button class="btn sidebar-toggler d-md-none" style="display: none;">
        <i class="bi bi-list fs-3 "></i>
    </button>

    <div class="d-flex align-items-center w-100 justify-content-between px-2 px-sm-4">
        <div>
            <a href="{{ route('admin.dashboard.page') }}" class=" user-profile-btn text-decoration-none">
                <i class="bi bi-columns-gap fs-5" style="rotate: 90deg;"></i>
                <span class="d-none d-sm-block">მთავარი</span>
            </a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- Contacts  -->
            <x-ui.link-icon route="contacts.index" icon="mailbox2-flag " />

            <!-- subscriptions  -->
            <x-ui.link-icon route="push.index" icon="bell-fill" />

            <!-- user avatar  -->
            <x-shared.user-avatar />
        </div>
    </div>
</nav>