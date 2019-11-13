<?php

namespace Clock\Baserepo\Test\DataSources\TUsers\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Clock\Baserepo\Repositories\BaseRepository;
use Clock\Baserepo\Test\DataSources\TUsers\TUser;

class TUserRepository extends BaseRepository
{
    /**
     * TUserRepository constructor.
     *
     * @param TUser $user
     */
    public function __construct(TUser $user)
    {
        parent::__construct($user);
    }

    /**
     * @param array $data
     * @return TUser
     */
    public function createUser(array $data) : TUser
    {
        $data['password'] = Hash::make($data['password']);
        return $this->create($data);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function updateUser(array $data) : bool
    {
        return $this->update($data);
    }

    /**
     * @param int $id
     * @return TUser
     */
    public function findUserById(int $id) : TUser
    {
        return $this->find($id);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function deleteUser() : bool
    {
        return $this->delete();
    }

    /**
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return Collection
     */
    public function listUsers($columns = ['*'], string $orderBy = 'id', string $sortBy = 'asc') : Collection
    {
        return $this->all($columns, $orderBy, $sortBy);
    }
}
