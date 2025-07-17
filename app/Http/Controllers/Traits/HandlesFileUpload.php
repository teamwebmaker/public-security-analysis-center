<?php
namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait HandlesFileUpload
{
   protected function handleFileUpload(
      Request $request,
      string $fieldName,
      string $destinationPath,
      string $oldFile = null
   ) {
      $fileName = null;

      if ($request->file($fieldName)) {
         $file = $request->file($fieldName);
         $fileName =
            uniqid() .
            "-" .
            time() .
            "." .
            $file->getClientOriginalExtension();
         $file->move(public_path($destinationPath), $fileName);

         if ($oldFile) {
            $oldFilePath = public_path($destinationPath . $oldFile);
            if (File::exists($oldFilePath)) {
               File::delete($oldFilePath);
            }
         }
      }

      return $fileName;
   }
}



?>