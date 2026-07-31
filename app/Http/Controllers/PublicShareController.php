<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Order;
use App\Models\PublicShare;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PublicShareController extends Controller
{
    public function show(string $token): View
    {
        $share = $this->resolveShare($token);

        if ($share->shareable instanceof Incident) {
            return view('public-shares.incident', [
                'share' => $share,
                'incident' => $share->shareable->load('branch.company'),
            ]);
        }

        if ($share->shareable instanceof Order) {
            return view('public-shares.order', [
                'share' => $share,
                'order' => $share->shareable->load('branch.company'),
            ]);
        }

        abort(404);
    }

    public function document(string $token): BinaryFileResponse
    {
        $share = $this->resolveShare($token);
        abort_unless($share->shareable instanceof Incident || $share->shareable instanceof Order, 404);

        return response()->download(
            Storage::disk('local')->path($share->shareable->document_path),
            $share->shareable->document_original_name,
            ['Content-Type' => $share->shareable->document_mime_type ?: 'application/octet-stream']
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
            !($share->shareable instanceof Incident || $share->shareable instanceof Order)
                || $share->shareable->isPublic(),
            404
        );

        if ($share->shareable instanceof Incident || $share->shareable instanceof Order) {
            abort_unless(Storage::disk('local')->exists($share->shareable->document_path), 404);
        }

        return $share;
    }
}
