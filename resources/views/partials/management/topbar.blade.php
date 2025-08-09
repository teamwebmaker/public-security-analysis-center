<nav class="topbar d-flex align-items-center justify-content-between p-1  shadow-sm ">
   <button class="btn sidebar-toggler d-md-none" style="display: none;">
      <i class="bi bi-list fs-3 "></i>
   </button>

   <div class="d-flex align-items-center w-100 justify-content-between px-2 px-sm-4">
      <div>
         {{ Auth::user()->full_name ?? ''}}
      </div>
      <div class="d-flex align-items-center gap-2">
         <!-- messages  -->
         {{-- <x-ui.link-icon icon="bell-fill " /> --}}
         <!-- user avatar -->
         <x-management.user-avatar-dropdown />
      </div>
   </div>
</nav>