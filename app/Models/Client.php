<?php

namespace App\Models;

use App\Http\Traits\Messagable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;

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
        'country', 'city', 'gender', 'age', 'birth_date', 'active', 'banned',
        'timezone'
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


    /**
     * change the route key to username
     *
     * @return void
     */
    public function getRouteKeyName()
    {
        return 'username';
    }


    /**
     * Get the user's age.
     *
     * @param  string  $value
     * @return string
     */
    public function getAgeAttribute()
    {
        $dateOfBirth = date('Y-m-d', strtotime($this->birth_date));
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        return $diff->format('%y');
    }

    /**
     * Set the user's birth date.
     *
     * @param  string  $value
     * @return void
     */
    public function setBirthDateAttribute($value)
    {
        $this->attributes['birth_date'] = date('Y-m-d H:i:s', strtotime($value));
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

    public function delete_photos()
    {
        if ($this->photos) {
            $urls = collect($this->photos)->flatten();
            $paths = $urls->all();
            foreach ($paths as $path) {
                $url = parse_url($path);
                if (file_exists(public_path($url['path']))) {
                    unlink(public_path($url['path']));
                }
            }
        }
    }

    /**
     * social login relationship
     *
     * @return void
     */
    public function socials()
    {
        return $this->hasOne('App\Models\Social');
    }


    /**
     * views relationship
     *
     * @return void
     */
    public function views()
    {
        return $this->hasMany('App\Models\View');
    }



    public function viewers()
    {
        return $this->belongsToMany(Models::classname(Client::class), Models::table('views'), 'client_id', 'viewer_id');
    }


    /**
     * likes relationship
     *
     * @return void
     */
    public function likes()
    {
        return $this->HasMany('App\Models\Like');
    }

    /**
     * your likes relationship
     *
     * @return void
     */
    public function myLikes()
    {
        return $this->belongsToMany(Models::classname(Client::class), Models::table('likes'), 'liker_id', 'client_id');
    }

    /**
     * likers relationship
     *
     * @return void
     */
    public function likers()
    {
        return $this->belongsToMany(Models::classname(Client::class), Models::table('likes'), 'client_id', 'liker_id');
    }


    /**
     * friends relationship
     *
     * @return object
     */
    public function friends()
    {
        return $this->belongsToMany(Models::classname(Client::class), Models::table('friends'), 'accepter_id', 'sender_id');
    }


    /**
     * friends relationship where you sent
     *
     * @return object
     */
    public function myFriends()
    {
        return $this->belongsToMany(Models::classname(Client::class), Models::table('friends'), 'sender_id', 'accepter_id');
    }



    public function friendsCount()
    {
        $one = $this->friends()->where('status', 'accepted')->count();

        $two = $this->myFriends()->where('status', 'accepted')->count();

        return $one + $two;
    }


    // function reverse_birthday( $years ){
    //     return date('Y-m-d', strtotime($years . ' years ago'));
    //     }
}
