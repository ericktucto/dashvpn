<?php

namespace App\Models;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $slug
 * @property string $otp
 * @property string $contents
 * @property DateTimeImmutable $exp
 */
final class SharedLink extends Model
{
    public $table = 'sharedlinks';
    public $dateFormat = 'Y-m-d H:i:s';
    public $timestamps = false;
    public $dates = ['exp'];
}

