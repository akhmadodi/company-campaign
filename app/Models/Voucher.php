<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'code',
        'locked_by'
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function customerVoucher()
    {
        return $this->hasOne(CustomerVoucher::class);
    }
}
