<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\PublicShare;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PublicShareController extends Controller
{
    public function show(string $token): View
    {
        $share = $this->resolveShare($token);

        abort_unless($share->shareable instanceof Incident, 404);
        $incident = $share->shareable->load('branch.company');

        return view('public-shares.incident', [
            'share' => $share,
            'incident' => $incident,
        ]);
    }

    public function document(string $token): BinaryFileResponse
    {
        $share = $this->resolveShare($token);
        abort_unless($share->shareable instanceof Incident, 404);

        $incident = $share->shareable;
        abort_unless(Storage::disk('local')->exists($incident->document_path), 404);

        return response()->download(
            Storage::disk('local')->path($incident->document_path),
            $incident->document_original_name,
            ['Content-Type' => $incident->document_mime_type ?: 'application/octet-stream']
        );
    }

    private function resolveShare(string $token): PublicShare
    {
        $share = PublicShare::query()
            ->where('token', $token)
            ->where('is_active', true)
            ->whereNull('revoked_at')
            ->with('shareable')
            ->firstOrFail();

        abort_unless(
            !($share->shareable instanceof Incident) || $share->shareable->isPublic(),
            404
        );

        return $share;
    }
}
