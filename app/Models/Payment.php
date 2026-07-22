<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Payment extends Model
{
    protected $connection = 'mongodb';

    protected $table = 'payments';

    protected $fillable = [
        'household_id',
        'waste_id',
        'amount',
        'payment_date',
        'status',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function confirm(): void
    {
        $this->status = 'paid';
        $this->payment_date = now();
        $this->save();
    }
}
