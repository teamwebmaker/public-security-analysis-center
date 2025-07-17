<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;

trait AppliesLocalScopes
{
   protected function applyLocalScopes(Model $model)
   {
      $query = $model->newQuery();

      if (property_exists($this, 'localScopes') && is_array($this->localScopes)) {
         foreach ($this->localScopes as $scope) {
            $query = $query->$scope();
         }
      }

      return $query;
   }
}
