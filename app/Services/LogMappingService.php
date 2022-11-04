<?php

namespace App\Services;

class LogMappingService
{
    private array $mapData;

    public function __construct()
    {
        $this->mapData = [
            'n' => 'name',
            'm' => 'message',
            'i' => 'info',
            's' => 'stack',
            'l' => 'location',
        ];
    }

    public function mapping(array $data, bool $removeUndefined = true): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->mapData)) {
                $result[$this->mapData[$key]] = $value;
            } elseif ($removeUndefined === false) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
