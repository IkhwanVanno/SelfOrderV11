<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'gross_amount',
        'payment_type',
        'payment_status',
        'transaction_status',
        'fraud_status',
        'status_code',
        'status_message',
        'signature_key',
        'midtrans_response',
        'transaction_time',
    ];

    protected $casts = [
        'midtrans_response' => 'array',
        'gross_amount' => 'decimal:2',
        'transaction_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship dengan Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if payment is successfully paid
     */
    public function isPaid(): bool
    {
        return in_array($this->transaction_status, ['capture', 'settlement']) 
               && $this->payment_status === 'paid';
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->transaction_status === 'pending' 
               && $this->payment_status === 'pending';
    }

    /**
     * Check if payment has failed
     */
    public function isFailed(): bool
    {
        return in_array($this->transaction_status, ['deny', 'cancel', 'expire', 'failure'])
               || $this->payment_status === 'failed';
    }

    /**
     * Check if payment is challenge (fraud detection)
     */
    public function isChallenge(): bool
    {
        return $this->fraud_status === 'challenge' 
               && $this->payment_status === 'challenge';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format((float)$this->gross_amount, 0, ',', '.');
    }

    /**
     * Get payment status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'paid' => 'green',
            'pending' => 'yellow',
            'challenge' => 'orange',
            'failed' => 'red',
            default => 'gray'
        };
    }
}