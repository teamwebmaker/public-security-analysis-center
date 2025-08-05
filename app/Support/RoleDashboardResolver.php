<?php


namespace App\Support;

use Illuminate\Support\Facades\Auth;

class RoleDashboardResolver
{
   public static function getController(): string
   {
      $role = Auth::user()?->getRoleName();

      return match ($role) {
         'company_leader' => \App\Http\Controllers\CompanyLeaderController::class,
         // 'responsible_person' => \App\Http\Controllers\ResponsiblePersonController::class,
         // 'worker' => \App\Http\Controllers\WorkerController::class,
         default => abort(403),
      };
   }
}