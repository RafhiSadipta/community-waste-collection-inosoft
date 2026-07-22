<?php

namespace App\Exceptions;

use RuntimeException;

abstract class DomainException extends RuntimeException
{
    protected int $status = 409;

    public function status(): int
    {
        return $this->status;
    }
}
