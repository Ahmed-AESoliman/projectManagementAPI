<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthenticatedUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return  [
            'userData' => [
                'id' => $this->id,
                'name' => $this->full_name,
                'email' => $this->email,
            ],
            'accessToken' => $this->createToken('auth-token')->accessToken,
        ];
    }
}
