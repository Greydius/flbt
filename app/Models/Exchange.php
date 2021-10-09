<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    use HasFactory;

    public function seller()
    {
        return $this->belongsTo(Client::class, 'seller_id');
    }

    public function buyer()
    {
        return $this->belongsTo(Client::class, 'buyer_id');
    }

    public function coin()
    {
        return $this->belongsTo(Coin::class);
    }

    public function fiat()
    {
        return $this->belongsTo(Fiat::class);
    }
}
