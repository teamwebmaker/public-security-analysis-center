<?php

namespace App\Services\PublicSharing;

use App\Models\PublicShare;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PublicShareService
{
    public function enable(Model $shareable, ?User $creator = null): PublicShare
    {
        $active = $shareable->publicShare()
            ->where('is_active', true)
            ->whereNull('revoked_at')
            ->first();

        if ($active) {
            return $active;
        }

        return $shareable->morphMany(PublicShare::class, 'shareable')->create([
            'token' => Str::random(64),
            'created_by_user_id' => $creator?->id,
            'is_active' => true,
            'revoked_at' => null,
        ]);
    }

    public function disable(Model $shareable): void
    {
        $shareable->morphMany(PublicShare::class, 'shareable')
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'revoked_at' => now(),
            ]);
    }
}
