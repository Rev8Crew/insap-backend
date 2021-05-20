<?php

namespace Tests\Unit;

use App\helpers\IsActiveHelper;
use App\Models\User;
use App\Models\UserInfo;
use App\Modules\User\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    private ?User $_user = null;
    private ?UserService $_userService = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->_userService = $this->app->make(UserService::class);
        // Take test user from seeder
        $this->_user = User::find(User::TEST_USER_ID);
    }

    public function testBasicCreate()
    {
        $array = [
            'name' => 'TestTest',
            'email' => 'testtest@mail.com',
            'password' => 'testtest'
        ];

        $this->_user = $this->_userService->create($array);

        $this->assertTrue( $this->_user instanceof User);
        $this->assertNotEmpty($this->_user);

        $this->assertEquals($array['name'], $this->_user->name);
        $this->assertEquals($array['email'], $this->_user->email);
        $this->assertEquals(IsActiveHelper::ACTIVE_ACTIVE, $this->_user->is_active);
        $this->assertTrue(Hash::check($array['password'], $this->_user->password));

        $this->assertNotNull($this->_user->user_info);
        $this->assertGreaterThan( 0, $this->_user->roles()->count());
    }

    public function testBasicDelete() {
        $userId = $this->_user->id;
        // Delete
        $this->_userService->delete($this->_user);

        $this->assertNull(User::find($userId));
        $this->assertNull(UserInfo::where('user_id', $userId)->first());
    }

    public function testBasicUpdate() {
        $updatesParams = [
            'name' => 'test_test_2',
            'email' => 'testtest2@mail.com',
            'is_active' => IsActiveHelper::ACTIVE_INACTIVE,
        ];

        $updatesInfoParams = [
            'test_attr' => 'value'
        ];

        $this->_userService->update($this->_user, $updatesParams, $updatesInfoParams);

        $this->assertEquals( $updatesParams['name'], $this->_user->name);
        $this->assertEquals( $updatesParams['email'], $this->_user->email);
        $this->assertEquals( $updatesParams['is_active'], $this->_user->is_active);

        $this->assertEquals( $updatesInfoParams['test_attr'], $this->_user->user_info->test_attr);
    }

    public function testBasicAddRoles() {
        $roles = [
            'super-admin',
            'test'
        ];

        // Do attach
        $this->_userService->attachRoles($this->_user, $roles);

        $this->assertTrue($this->_user->hasAllRoles($roles));
    }

    public function testBasicRemoveRoles() {
        $roles = [
            'super-admin',
            'test'
        ];

        // Do attach
        $this->_userService->removeRoles($this->_user, $roles);

        $this->assertFalse($this->_user->hasAllRoles($roles));
    }

    public function testBasicActivateUser() {
        $this->_userService->activate($this->_user);

        $this->assertEquals(IsActiveHelper::ACTIVE_ACTIVE, $this->_user->is_active);
    }
}
