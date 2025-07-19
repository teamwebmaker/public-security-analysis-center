<div class="dropdown">
    <button
        class="nav-link dropdown-toggle gap-1 border-0 p-0  bg-transparent  d-flex text-transparent align-items-center position-relative"
        id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="{{ asset('images/users/default_user_image.png') }}" alt="User image" width="34" height="34"
            class="rounded-circle shadow-sm">
    </button>
    <ul class="dropdown-menu p-1 dropdown-menu-end rounded-3 mx-0 shadow" style="max-width: 200px;"
        aria-labelledby="userDropdown">
        <!-- divider line -->
        <!-- <li> <hr class="dropdown-divider"> </li>  -->
        {{ $slot }}
        <!-- Log out-->
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item rounded-2">გასვლა</button>
            </form>
        </li>
    </ul>
</div>