import { getOne } from "./heplers.js";
import './bootstrap/bootstrapValidation.js' // Bootstrap form validation
document.addEventListener('DOMContentLoaded', () => {

   const sidebar = getOne('.sidebar');
   const dashboardWrapper = getOne('.dashboard-wrapper');
   const sidebarToggler = getOne('.sidebar-toggler');
   const closeButton = getOne('.btn-close');

   if (!sidebar || !dashboardWrapper) return;

   const toggleSidebar = () => {
      sidebar.classList.toggle('active');
   };

   const closeSidebar = () => {
      sidebar.classList.remove('active');
   };

   const handleOutsideClick = (e) => {
      const clickedInsideSidebar = sidebar.contains(e.target);
      const clickedToggler = e.target.closest('.sidebar-toggler');
      const clickedCloseBtn = e.target.closest('.btn-close');

      if (!clickedInsideSidebar && !clickedToggler && !clickedCloseBtn) {
         closeSidebar();
      }
   };

   dashboardWrapper.addEventListener('click', handleOutsideClick);
   sidebarToggler?.addEventListener('click', toggleSidebar);
   closeButton?.addEventListener('click', toggleSidebar);
});
