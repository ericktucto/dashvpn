<?php

namespace App\Definitions;

use App\Domain\Auth;
use App\Domain\AuthInterface;

final class AuthDefinition
{
    static ?AuthInterface $instance = null;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function create(): AuthInterface
    {
        if (static::$instance) {
            return static::$instance;
        }
        return static::$instance = new Auth();
    }
}
