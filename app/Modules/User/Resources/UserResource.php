<?php

namespace App\Modules\User\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="created_at", type="string"),
 *     @OA\Property(property="updated_at", type="string"),
 *     @OA\Property(property="token", type="string", nullable=true)
 * )
 *
 * @mixin User
 */
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

            'userInfo' => $this->userInfo
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
