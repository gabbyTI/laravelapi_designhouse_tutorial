<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\UnspecifiedModel;
use App\Repositories\Contracts\IBase;
use Exception;

abstract class BaseRepository implements IBase
{
    protected $model;

    public function __construct()
    {
        $this->model = $this->getModelClass();
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        $result = $this->model->findOrFail($id);
        return $result;
    }
    public function findWhere($column, $value)
    {
        return $this->model->where($column, $value)->get();
    }
    public function findWhereFirst($column, $value)
    {
        return $this->model->where($column, $value)->firstOrFail();
    }
    public function paginate($perPage = 10)
    {
        return $this->model->paginate($perPage);
    }
    public function create(array $data)
    {
        $record = $this->model->create($data);
        return $record;
    }
    public function update($id, array $data)
    {
        $record = $this->find($id);

        $record->update($data);
        return $record;
    }
    public function delete($id)
    {
        $record = $this->find($id);

        return $record->delete();
    }

    protected function getModelClass()
    {
        if (!method_exists($this, 'model')) {
            throw new UnspecifiedModel();
        }
        return app()->make($this->model());
    }
}
