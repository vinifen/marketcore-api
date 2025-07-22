<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

/**
 * @property string $name
 * @property int $user_id
 * @property string $description
 */
class Test extends Model
{
    /** @use HasFactory<\Database\Factories\TestFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_id'
    ];
    
    /**
     * @return BelongsTo<User, Test>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
