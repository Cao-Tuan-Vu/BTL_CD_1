<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public const PAYMENT_METHOD_CASH = 'cash';

    public const PAYMENT_METHOD_MOMO = 'momo';

    public const PAYMENT_METHOD_VNPAY = 'vnpay';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected const STATUS_LABELS = [
        self::STATUS_PENDING => 'Chờ xử lý',
        self::STATUS_PROCESSING => 'Đang xử lý',
        self::STATUS_COMPLETED => 'Đã hoàn tất',
        self::STATUS_CANCELLED => 'Đã hủy',
    ];

    protected const STATUS_BADGE_CLASSES = [
        self::STATUS_PENDING => 'info',
        self::STATUS_PROCESSING => 'warn',
        self::STATUS_COMPLETED => 'ok',
        self::STATUS_CANCELLED => 'stop',
    ];

    protected const PAYMENT_METHOD_LABELS = [
        self::PAYMENT_METHOD_CASH => 'Tiền mặt (COD)',
        self::PAYMENT_METHOD_MOMO => 'Ví MoMo',
        self::PAYMENT_METHOD_VNPAY => 'VNPay',
    ];

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'shipping_phone',
        'shipping_address',
        'payment_method',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return self::STATUS_LABELS;
    }

    public static function labelForStatus(?string $status): string
    {
        if (! is_string($status) || $status === '') {
            return 'Không xác định';
        }

        return self::STATUS_LABELS[$status] ?? $status;
    }

    public static function badgeClassForStatus(?string $status): string
    {
        if (! is_string($status) || $status === '') {
            return 'info';
        }

        return self::STATUS_BADGE_CLASSES[$status] ?? 'info';
    }

    /**
     * @return array<int, string>
     */
    public static function paymentMethods(): array
    {
        return [
            self::PAYMENT_METHOD_CASH,
            self::PAYMENT_METHOD_MOMO,
            self::PAYMENT_METHOD_VNPAY,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function paymentMethodLabels(): array
    {
        return self::PAYMENT_METHOD_LABELS;
    }

    public static function labelForPaymentMethod(?string $paymentMethod): string
    {
        if (! is_string($paymentMethod) || $paymentMethod === '') {
            return 'Không xác định';
        }

        return self::PAYMENT_METHOD_LABELS[$paymentMethod] ?? $paymentMethod;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}
