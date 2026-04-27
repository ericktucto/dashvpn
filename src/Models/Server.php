<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $ip
 * @property int $listenPort
 * @property string $address
 * @property string $dns
 * @property string $postUp
 * @property string $postDown
 * @property string $interface
 */
final class Server extends Model
{
    public $table = 'server';
    public $dateFormat = 'Y-m-d H:i:s';
    public $timestamps = false;

    /**
     * @return list<string>
     */
    public function getPostUpArray(): array
    {
        return explode('; ', $this->postUp);
    }

    /**
     * @return list<string>
     */
    public function getPostDownArray(): array
    {
        return explode('; ', $this->postDown);
    }
}

