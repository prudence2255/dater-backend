<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id', 'accepter_id', 'status', 'read_at'];


/**
 * friends who accepted me relationship
 *
 * @return void
 */
    public function friendOfMe(){
        return $this->belongsTo('App\Models\Client', 'accepter_id');
    }

/**
 * friends I accepted relationship
 *
 * @return void
 */
public function myFriend(){
    return $this->belongsTo('App\Models\Client', 'sender_id');
}
}
