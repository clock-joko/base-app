<?php
namespace ClockIt\Baserepo\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    /**
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return mixed
     */
    public function all($columns = ['*'], string $orderBy = 'id', string $sortBy = 'asc');

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param array $data
     * @return mixed
     */
    public function findBy(array $data);

    /**
     * @param array $data
     * @return mixed
     */
    public function findFirst(array $data);

    /**
     * @param array $attributes
     * @return bool
     */
    public function save(array $attributes) : bool;

    /**
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes);

    /**
     * @param array $attributes
     * @return bool
     */
    public function update(array $attributes) : bool;

    /**
     * @return bool
     * @throws \Exception
     */
    public function delete() : bool;

    /**
     * @param $ids
     * @return int
     */
    public function destroy($ids) : int;
}
