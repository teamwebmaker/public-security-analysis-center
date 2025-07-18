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
         <a href="{{ route('contacts.index') }}" class="btn logout-btn">
            <i class="bi bi-mailbox2-flag fs-4"></i>
         </a>
         <!-- subscriptions  -->
         <a href="{{ route('push.index') }}" class="btn logout-btn">
            <i class="bi bi-bell-fill fs-4"></i>
         </a>

         <!-- Logout -->
         <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="btn logout-btn d-flex gap-2 align-items-center ">
               <i class="bi bi-box-arrow-right fs-5 "></i>
               <span class="fs-8 d-none d-sm-block">გასვლა</span>
            </button>
         </form>
      </div>

   </div>

</nav>