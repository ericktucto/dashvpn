<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $ip
 * @property int $listenPort
 * @property string $address
 * @property string $dns
 */
final class Server extends Model
{
    public $table = 'server';
    public $dateFormat = 'Y-m-d H:i:s';
    public $timestamps = false;
}

