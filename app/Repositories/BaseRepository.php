<?php

namespace App\Repositories;

use App\Interfaces\CrudRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements CrudRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $model = $this->find($id);
        $model->update($data);

        return $model;
    }

    public function delete($id)
    {
        return $this->model->findOrFail($id)->delete();
    }
}
