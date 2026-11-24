<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** Pajak restoran PB1 = 10% dari (subtotal - diskon). */
    public const TAX_RATE = 0.10;

    public const STATUSES = [
        'diproses' => 'Diproses',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    public const PAYMENT_METHODS = [
        'tunai' => 'Tunai',
        'qris' => 'QRIS',
        'transfer' => 'Transfer Bank',
        'debit' => 'Kartu Debit',
    ];

    public const ORDER_TYPES = [
        'dine_in' => 'Dine-In',
        'takeaway' => 'Takeaway',
    ];

    protected $fillable = [
        'invoice_number',
        'user_id',
        'tipe_pesanan',
        'nomor_meja',
        'nama_pemesan',
        'total_subtotal',
        'pajak_nominal',
        'diskon_nominal',
        'grand_total',
        'metode_bayar',
        'uang_dibayar',
        'uang_kembali',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_subtotal' => 'decimal:2',
            'pajak_nominal' => 'decimal:2',
            'diskon_nominal' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'uang_dibayar' => 'decimal:2',
            'uang_kembali' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function paymentLabel(): string
    {
        return self::PAYMENT_METHODS[$this->metode_bayar] ?? $this->metode_bayar;
    }

    public function typeLabel(): string
    {
        return self::ORDER_TYPES[$this->tipe_pesanan] ?? $this->tipe_pesanan;
    }
}
