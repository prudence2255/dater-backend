<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
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
            'birth_date' => $this->birth_date,
            'active' => boolval($this->active),
            'country' => $this->country,
            'city' => $this->city,
            'gender' => $this->gender,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'profile_pictures' => $this->profilePictures()->latest('created_at')->first(),
        ];
    }
}
