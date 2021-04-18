<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Http\Traits\Messagable;

class Client extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable, Messagable;

    protected $guard = 'client';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'username',
        'country', 'city', 'gender', 'age',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

 /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getRouteKeyName(){
        return 'username';
    }

      ///client meta
      public function meta()
      {
       return $this->hasOne('App\Models\ClientMeta');
     }

    /**
      * Get all of the photos for the Client
      *
      * @return \Illuminate\Database\Eloquent\Relations\HasMany
      */
     public function photos()
     {
         return $this->hasMany(Photo::class);
     }



      /**
      * Get profile picture for the Client
      *
      * @return \Illuminate\Database\Eloquent\Relations\HasMany
      */
     public function profilePictures()
     {
         return $this->hasMany(ProfilePicture::class);
     }

     /**
      * delete photos
      *
      * @return void
      */

     public function delete_photos(){
        if($this->photos){
            $urls = collect($this->photos)->flatten();
            $paths = $urls->all();
            foreach ($paths as $path) {
                  $url = parse_url($path);
                  if(file_exists(public_path($url['path']))){
                    unlink(public_path($url['path']));
                  }
                }
        }
    }

    public function socials(){
        return $this->hasOne('App\Models\Social');
    }


}
