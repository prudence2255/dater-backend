<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThreadResource extends JsonResource
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
                'users' => $this->users,
                'participants' => $this->participants,
                'extras' => collect($this->users)->map(function($user){
                    return [
                        'profile_picture' => $user->profilePictures()->latest('created_at')->first(),
                         'count' => $this->userUnreadMessagesCount($user->id),
                         'user_id' => $user->id
                    ];
                }),
                'messages' => $this->messages
        ];
    }
}
