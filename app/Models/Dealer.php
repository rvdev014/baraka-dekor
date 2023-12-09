<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * class Dealer
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 *
 *
 * @mixin Builder
 */
class Dealer extends Model
{
    use HasFactory;

    protected $table = 'dealers';

    protected $fillable = [
        'name',
        'phone',
    ];
}
