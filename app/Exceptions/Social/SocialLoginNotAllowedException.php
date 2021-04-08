<?php

namespace App\Exceptions\Social;

use Exception;

class SocialLoginNotAllowedException extends Exception
{
    public array $values;

    public function __construct($message, ...$values)
    {
        $this->values = $values ?? [];
        parent::__construct($message);
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
