<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property string $street
 * @property int $number
 * @property string|null $complement
 * @property string $city
 * @property string $state
 * @property int $postal_code
 * @property string $country
 * @property string $email
 */
class Address extends Model
{
    /** @use HasFactory<\Database\Factories\AddressFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'street',
        'city',
        'state',
        'postal_code',
        'country',
        'number',
        'complement',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Address>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
