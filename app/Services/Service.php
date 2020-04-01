<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class Service
{
    protected $model;
    protected $per_page = 20;

    public function listing()
    {
        return $this->model->paginate($this->per_page);
    }

    public function get($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update(Model $model, $data)
    {
        return $model->update($data);
    }

    public function delete($id)
    {
        return $this->model->delete($id);
    }
}
