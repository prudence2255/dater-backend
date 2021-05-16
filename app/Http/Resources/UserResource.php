<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'age' => $this->age,
            'country' => $this->country,
            'city' => $this->city,
            'gender' => $this->gender,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'profile_pictures' => $this->profilePictures()->latest('created_at')->first(),
            'meta' => $this->meta,
            'friends_count' => $this->friendsCount(),
            'likes_count' => $this->likers->count()
        ];
    }

}
