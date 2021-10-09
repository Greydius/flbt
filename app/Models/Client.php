<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['tg_id', 'last_position'];

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function sales()
    {
        return $this->hasMany(Exchange::class, 'seller_id');
    }

    public function buys()
    {
        return $this->hasMany(Exchange::class, 'buyer_id');
    }
}
