<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\UserPurchase
 *
 * @property int $id
 * @property int $user_id
 * @property int $price
 *
 * @property User $user
 *
 * @mixin Builder
 */
class UserPurchase extends Model
{
    use HasFactory;

    protected $table = 'user_purchases';

    protected $fillable = [
        'user_id',
        'price',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
