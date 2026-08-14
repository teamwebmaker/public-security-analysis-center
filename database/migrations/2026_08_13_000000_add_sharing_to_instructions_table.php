<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructions', function (Blueprint $table) {
            $table->string('document_original_name')->nullable()->after('document');
            $table->string('document_mime_type')->nullable()->after('document_original_name');
            $table->string('document_visibility', 20)->default('private')->after('document_mime_type');
        });

        DB::table('instructions')
            ->select(['id', 'document'])
            ->orderBy('id')
            ->each(function ($instruction): void {
                $publicPath = normalize_public_upload_path(
                    $instruction->document,
                    'documents/instructions'
                );

                if (! $publicPath || filter_var($publicPath, FILTER_VALIDATE_URL)) {
                    return;
                }

                $originalName = basename($publicPath);
                $localPath = 'instructions/'.$instruction->id.'-'.$originalName;
                $mimeType = null;
                $movedToProtectedStorage = false;

                if (Storage::disk('public')->exists($publicPath)) {
                    $stream = Storage::disk('public')->readStream($publicPath);

                    if (! is_resource($stream)) {
                        throw new RuntimeException("Unable to read legacy instruction document [{$publicPath}].");
                    }

                    $stored = Storage::disk('local')->put($localPath, $stream);
                    fclose($stream);

                    if (! $stored) {
                        throw new RuntimeException("Unable to protect legacy instruction document [{$publicPath}].");
                    }

                    $mimeType = Storage::disk('local')->mimeType($localPath) ?: null;
                    $movedToProtectedStorage = true;
                } else {
                    $localPath = $instruction->document;
                }

                DB::table('instructions')
                    ->where('id', $instruction->id)
                    ->update([
                        'document' => $localPath,
                        'document_original_name' => $originalName,
                        'document_mime_type' => $mimeType,
                    ]);

                if ($movedToProtectedStorage) {
                    Storage::disk('public')->delete($publicPath);

                    if (Storage::disk('public')->exists($publicPath)) {
                        throw new RuntimeException("Unable to remove public instruction document [{$publicPath}].");
                    }
                }
            });
    }

    public function down(): void
    {
        DB::table('instructions')
            ->select(['id', 'document', 'document_original_name'])
            ->orderBy('id')
            ->each(function ($instruction): void {
                if (! Storage::disk('local')->exists($instruction->document)) {
                    return;
                }

                $fileName = $instruction->document_original_name ?: basename($instruction->document);
                $publicPath = 'documents/instructions/'.$fileName;
                $stream = Storage::disk('local')->readStream($instruction->document);

                if (! is_resource($stream)) {
                    return;
                }

                $stored = Storage::disk('public')->put($publicPath, $stream);
                fclose($stream);

                if ($stored) {
                    DB::table('instructions')->where('id', $instruction->id)->update([
                        'document' => $publicPath,
                    ]);
                    Storage::disk('local')->delete($instruction->document);
                }
            });

        Schema::table('instructions', function (Blueprint $table) {
            $table->dropColumn([
                'document_original_name',
                'document_mime_type',
                'document_visibility',
            ]);
        });
    }
};
