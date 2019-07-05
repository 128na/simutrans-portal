<?php
namespace App\Traits;

trait JsonFieldable
{
    public function getJsonableField()
    {
        throw new \Error('jsonable_field missing');
    }
    /*
    |--------------------------------------------------------------------------
    | 一般
    |--------------------------------------------------------------------------
    */
    public function getContents($key, $default = null)
    {
        $field = $this->getJsonableField();
        return data_get($this->$field, $key, $default);
    }
    public function setContents($key, $value)
    {
        $field = $this->getJsonableField();
        $tmp = $this->$field;
        $tmp[$key] = is_numeric($value) ? intval($value) : $value;
        $this->$field = $tmp;
    }
}
