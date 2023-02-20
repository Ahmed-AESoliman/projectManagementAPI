<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $userAvatar = $this->attachments()->where('file_name', 'user avatar')->first();
        return [
            'id' => $this->id,
            "fullName" => $this->full_name,
            'role' => $this->role,
            'active' => $this->active,
            'userMobile' => $this->user_mobile,
            'userAvatar' => $userAvatar->file_link ??  "",
            "isMangement" => $this->is_mangement_team,
        ];
    }
}
