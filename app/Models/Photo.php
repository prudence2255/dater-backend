<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = ['photos', 'client_id'];


    protected $casts = [
        'photos' => 'array',
    ];
        /**
         * Get the client that owns the Photo
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function client()
        {
            return $this->belongsTo(Client::class);
        }

}
