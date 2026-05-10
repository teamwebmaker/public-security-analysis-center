<?php
namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

trait HandlesFileUpload
{
   protected function handleFileUpload(
      Request $request,
      string $fieldName,
      string $destinationPath,
      ?string $oldFile = null
   ) {
      if ($request->hasFile($fieldName)) {
         $file = $request->file($fieldName);
         $fileName =
            uniqid() .
            "-" .
            time() .
            "." .
            $file->getClientOriginalExtension();
         $directory = trim($destinationPath, '/');
         $storedPath = $file->storeAs($directory, $fileName, 'public');

         if (!$storedPath) {
            throw new RuntimeException("Failed to store uploaded file for field [{$fieldName}].");
         }

         if ($oldFile) {
            $this->deleteUploadedFile($oldFile, $directory);
         }

         return $storedPath;
      }

      return null;
   }

   protected function deleteUploadedFile(?string $filePath, ?string $legacyDirectory = null): void
   {
      $normalizedPath = normalize_public_upload_path($filePath, $legacyDirectory);

      if (!$normalizedPath || filter_var($normalizedPath, FILTER_VALIDATE_URL)) {
         return;
      }

      Storage::disk('public')->delete($normalizedPath);
   }
}
