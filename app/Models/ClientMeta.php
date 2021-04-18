<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientMeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'want', 'interest', 'status', 'education', 'profession',
        'height', 'eye_color', 'hair_color', 'self_summary',
        'f_music', 'f_shows', 'f_movies', 'f_books',
        'religion', 'client_id'
    ];


    public function client(){
        return $this->belongsTo('App\Models\Client');
    }

}



