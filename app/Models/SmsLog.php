<?php

namespace App\Models;

use App\Services\Messages\MessageStoreService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Throwable;

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
            $pending = self::statusNumber('pending') ?? 0;
            $undelivered = self::statusNumber('undelivered') ?? 2;
            $status = (int) $smsLog->status;

            if (!in_array($status, [$pending, $undelivered], true)) {
                return;
            }

            $lines = [
                "SMS ლოგი შეიქმნა სტატუსით: " . self::statusName($status),
                "ლოგის ID: #{$smsLog->id}",
                "ნომერი: {$smsLog->destination}",
                "ტიპი: " . self::smsnoTypeName((int) $smsLog->smsno),
                "პროვაიდერი: {$smsLog->provider}",
                "შეტყობინება: {$smsLog->content}",
            ];

            try {
                app(MessageStoreService::class)->createAndDispatch([
                    'source' => 'system',
                    'subject' => "SMS სტატუსი: " . self::statusName($status),
                    'message' => implode("\n", $lines),
                ]);
            } catch (Throwable $e) {
                Log::error('Failed to create system message for sms_logs created event', [
                    'sms_log_id' => $smsLog->id,
                    'status' => $status,
                    'error' => $e->getMessage(),
                ]);
            }
        });
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
