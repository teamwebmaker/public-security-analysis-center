<?php

namespace App\Models;

use App\Services\Sms\SmsFailureSystemNotifier;
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
        'event_type',
        'entity_id',
        'recipient_type',
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

    protected static function booted(): void
    {
        static::created(function (SmsLog $smsLog): void {
            $undelivered = self::statusNumber('undelivered') ?? 2;
            if ((int) $smsLog->status !== $undelivered) {
                return;
            }

            self::reportUndelivered($smsLog);
        });

        static::updated(function (SmsLog $smsLog): void {
            $undelivered = self::statusNumber('undelivered') ?? 2;
            if (! $smsLog->wasChanged('status') || (int) $smsLog->status !== $undelivered) {
                return;
            }

            self::reportUndelivered($smsLog);
        });
    }

    private static function reportUndelivered(SmsLog $smsLog): void
    {
        $reason = data_get($smsLog->provider_response, 'data.message')
            ?? data_get($smsLog->provider_response, 'data.data.0.message')
            ?? data_get($smsLog->provider_response, 'data.0.message')
            ?? 'პროვაიდერმა SMS ვერ მიაწოდა.';
        $reason = is_scalar($reason) && trim((string) $reason) !== ''
            ? trim((string) $reason)
            : 'პროვაიდერმა SMS ვერ მიაწოდა.';

        app(SmsFailureSystemNotifier::class)->report(
            'SMS ვერ გაიგზავნა',
            [
                'SMS ვერ გაიგზავნა ან ვერ მიეწოდა.',
                'მოვლენა: ' . ($smsLog->event_type ?: '—'),
                "ნომერი: {$smsLog->destination}",
                'პროვაიდერი: ' . ($smsLog->provider ?: '—'),
                "მიზეზი: {$reason}",
            ],
            ['sms_log_id' => $smsLog->id]
        );
    }

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

    /**
     * number → smsno type name
     */
    public static function smsnoTypeName(int $number): string
    {
        return self::SMSNO_TYPES[$number] ?? 'unknown';
    }

    /**
     * name → smsno number
     */
    public static function smsnoTypeNumber(string $name): ?int
    {
        $flipped = array_change_key_case(array_flip(self::SMSNO_TYPES), CASE_LOWER);

        return $flipped[strtolower($name)] ?? null;
    }
}
