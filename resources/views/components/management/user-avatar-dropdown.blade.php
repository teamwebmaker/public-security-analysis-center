<x-shared.user-avatar>
    <li>
        @if (request()->is('management*'))
            <a class="dropdown-item rounded-2"
                href="{{ route('home.page') }}">{{__('static.user_avatar_dropdown.home')}}</a>
        @else
            <a class="dropdown-item rounded-2"
                href="{{ route('management.dashboard.page') }}">{{__('static.user_avatar_dropdown.dashboard')}}</a>
        @endif
    </li>
</x-shared.user-avatar>