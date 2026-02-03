<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Casts\EncryptedDouble;

class Sneeze extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'sneeze_date',
        'sneeze_time',
        'location',
        'latitude',
        'longitude',
        'count',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sneeze_date' => 'date',
            'intensity' => 'integer',
            'latitude' => EncryptedDouble::class,
            'longitude' => EncryptedDouble::class,
            'count' => 'integer',
        ];
    }

    /**
     * Get the user that recorded this sneeze.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
