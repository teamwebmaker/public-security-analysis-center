<?php

namespace App\Http\Controllers\Traits;

trait SyncsRelations
{
   /**
    * Sync multiple relations from input data, even if the key is missing (defaults to []).
    * key is missing in case if we empty the input, for deleting purposes 
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @param  array  $data
    * @param  array  $relations ['relationMethod' => 'input_key']
    * @return void
    */
   public function syncRelations($model, array $data, array $relations)
   {
      foreach ($relations as $relation => $key) {
         // If key is not present, treat as empty array
         $values = array_key_exists($key, $data)
            ? $data[$key]
            : [];

         // Convert null to empty array just in case
         $model->{$relation}()->sync($values ?? []);
      }
   }
}