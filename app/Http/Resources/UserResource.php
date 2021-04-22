<?php

namespace App\Http\Resources;

use App\Models\UserInfo;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/** @mixin \App\Models\User */
class UserResource extends JsonResource
{
    private string $_token = '';
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d.m.Y H:i:s'),
            'token' => $this->_token,

            'userInfo' => UserInfo::where('user_id', $this->id)->first()
        ];
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->_token = $token;
    }
}
