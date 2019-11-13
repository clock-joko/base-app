<?php

namespace Clock\Baserepo\Test\Unit;

use Clock\Baserepo\Test\DataSources\TUsers\TUser;
use Clock\Baserepo\Test\DataSources\TUsers\Repositories\TUserRepository;
use Clock\Baserepo\Test\TestCase;

class UserUnitTest extends TestCase
{
    /**
     * テストデータ作成
     * @return mixed
     */
    private function createData()
    {
        $data = [
            'name' => 'Clock It',
            'email' => 'clock@clock-it.jp',
            'password' => 'secret'
        ];

        $user = factory(TUser::class)->create($data);

        return $user;
    }
    /**
     * Create User
     */
    public function testCreateUser()
    {
        $data = [
            'name' => 'Clock It',
            'email' => 'clock@clock-it.jp',
            'password' => 'secret'
        ];
        $userRepo = new TUserRepository(new TUser);
        $user = $userRepo->createUser($data);
        $this->assertInstanceOf(TUser::class, $user);
        $this->assertEquals($data['name'], $user->name);
        $this->assertEquals($data['email'], $user->email);
    }

    /**
     * Update User
     */
    public function testUpdateUser()
    {
        $user = $this->createData();

        $update = [
            'name' => 'Test Test'
        ];

        $userRepo = new TUserRepository($user);
        $updated = $userRepo->updateUser($update);

        $this->assertTrue($updated);
    }

    /**
     * Delete User
     *
     * @throws \Exception
     */
    public function testDeleteUser()
    {
        $user = $this->createData();

        $userRepo = new TUserRepository($user);
        $deleted = $userRepo->deleteUser();
        $this->assertTrue($deleted);
    }

    /**
     * Show User
     */
    public function testShowUser()
    {
        $user = $this->createData();

        $userRepo = new TUserRepository($user);
        $found = $userRepo->findUserById($user->id);

        $this->assertInstanceOf(TUser::class, $found);
        $this->assertEquals($user->name, $found->name);
        $this->assertEquals($user->email, $found->email);
    }

    /**
     * List User
     */
    public function testListUser()
    {
        $user = $this->createData();

        $userRepo = new TUserRepository($user);
        $users = $userRepo->listUsers();

        $users->each(function (TUser $item) use ($user) {
            $this->assertEquals($user->name, $item->name);
            $this->assertEquals($user->email, $item->email);
        });
    }
}
