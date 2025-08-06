<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property UserRole $role
 */
class User extends Authenticatable
{
    use HasApiTokens;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * @return HasMany<Address, User>
     */
    protected function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * @return HasOne<Cart, User>
     */
    protected function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isModerator(): bool
    {
        return $this->role === UserRole::MODERATOR;
    }

    public function isStaff(): bool
    {
        return in_array($this->role, [
            UserRole::ADMIN,
            UserRole::MODERATOR,
        ], true);
    }

    protected static function booted()
    {
        static::created(function ($user) {
            Cart::create(['user_id' => $user->id]);
        });
    }
}


    // protected static function booted()
    // {
    //     static::created(function ($user) {
    //         Cart::create(['user_id' => $user->id]);
    //     });

    //     static::deleting(function ($user) {
    //         $user->addresses()->each(function ($address) {
    //             $address->delete();
    //         });

    //         if ($user->cart) {
    //             $user->cart->delete();
    //         }
    //     });

    //     static::restoring(function ($user) {
    //         $user->addresses()->withTrashed()->each(function ($address) {
    //             $address->restore();
    //         });

    //         if ($user->cart()->withTrashed()->exists()) {
    //             $user->cart()->withTrashed()->first()->restore();
    //         }
    //     });
    // }
