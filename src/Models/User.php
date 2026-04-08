<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $timestamps = false;
    protected $hidden = ['password'];

    protected function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = password_hash($value, PASSWORD_BCRYPT);
    }

    public function isPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}
