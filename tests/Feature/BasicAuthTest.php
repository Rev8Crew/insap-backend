<?php

namespace Tests\Feature;

use App\Models\Response\ResponseStatus;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BasicAuthTest extends TestCase
{
    private string $xsrf_token = '';
    /**
     *
     */
    public function testBasicAuth()
    {
        $response = $this->postJson('web/auth/login', [
            'email' => "admin@admin.com",
            'password' => "rootadmin"
        ]);

        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) =>
            $json->where('data.id', 1)
                ->where('data.email', 'admin@admin.com')
                ->where('data.userInfo.user_id', 1)
                ->etc()
        );

    }

    public function testBasicMe() {
        $user = User::find(User::ROOT_USER_ID);
        $response = $this->actingAs($user)->getJson('web/auth/me');

        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) =>
        $json->where('data.id', 1)
            ->where('data.email', 'admin@admin.com')
            ->where('data.userInfo.user_id', 1)
            ->etc()
        );
    }

    public function testBasicLogout() {
        $user = User::find(User::TEST_USER_ID);
        $response = $this->actingAs($user)->postJson('web/auth/logout');

        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) =>
        $json->where('status', ResponseStatus::STATUS_OK)
            ->etc()
        );
    }
}
