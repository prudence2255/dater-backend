<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'viewer_id', 'read_at'];

    
    public function client(){
        return $this->belongsTo('App\Models\Client');
    }
}
