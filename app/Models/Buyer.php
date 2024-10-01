<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Buyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'email',
        'phone_number',
        'first_name',
        'last_name',
        'address',
        'city',
        'country',
        'state_province',
        'postal_code',
        'delivery_method',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
