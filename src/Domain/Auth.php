<?php

namespace App\Domain;

use App\Models\User;
use Override;

final class Auth implements AuthInterface
{
    public function __construct(
        protected ?User $user = null,
    ) {
    }

    #[Override]
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    #[Override]
    public function user(): ?User
    {
        return $this->user;
    }
}
