<?php

namespace App\Domain;

use App\Models\User;

interface AuthInterface
{
    public function setUser(?User $user): void;
    public function user(): ?User;
}
