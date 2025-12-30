<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'provider_message_id',
        'destination',
        'content',
        'smsno',
        'status',
        'provider_response',
        'sent_at',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'sent_at' => 'datetime',
        'status' => 'integer',
        'smsno' => 'integer',
    ];

    // Sender ge statuses
    public const STATUSES = [
        0 => 'pending',
        1 => 'delivered',
        2 => 'undelivered',
    ];

    public const SMSNO_TYPES = [
        1 => 'advertising',
        2 => 'information',
    ];


    /**
     * name → number
     */
    public static function statusNumber(string $name): ?int
    {
        $flipped = array_change_key_case(array_flip(self::STATUSES), CASE_LOWER);

        return $flipped[strtolower($name)] ?? null;
    }

    /**
     * number → name (static helper)
     */
    public static function statusName(int $number): string
    {
        return self::STATUSES[$number] ?? 'unknown';
    }
}
