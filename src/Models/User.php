<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $password
 * @property string $username
 */
final class User extends Model
{
    public $table = 'users';
    public $dateFormat = 'Y-m-d H:i:s';
    public $timestamps = false;
    protected $hidden = ['password'];

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    protected function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = password_hash($value, PASSWORD_BCRYPT);
    }

    public function isPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}
