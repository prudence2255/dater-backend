<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThreadsResource extends JsonResource
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
            'count' => $this->userUnreadMessagesCount(request()->user()->id),
            'users' => $this->users,
            'participants' => $this->participants,
            'profile_pictures' => collect($this->users)->map(function($user){
                return [
                    'profile_picture' => $user->profilePictures()->latest('created_at')->first()
                ];
            }),
            'messages' => $this->messages
        ];
    }
}
