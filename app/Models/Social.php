<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'provider_name',
        'provider_id',
    ];

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }
}
