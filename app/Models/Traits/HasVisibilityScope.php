<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasVisibilityScope
{
   /**
    * This method adds a global scope to ensure that queries only include
    * records with visibility set to '1', unless the request is within
    * the admin section (i.e., the URL matches 'admin/*')
    * | This scope is automatically applied to any model using this trait,
    * and only affects Eloquent-based queries (not the query builder).
    */

   protected static function bootHasVisibilityScope(): void
   {
      static::addGlobalScope('visible', function (Builder $builder) {
         if (!request()->is('admin/*')) {
            $builder->where('visibility', '1');
         }
      });
   }
}

?>