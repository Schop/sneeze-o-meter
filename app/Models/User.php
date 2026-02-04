<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'use_precise_location',
        'custom_locations',
        'show_in_leaderboard',
        'profile_picture',
        'gender',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'custom_locations' => 'array',
            'show_in_leaderboard' => 'boolean',
        ];
    }

    /**
     * Get the profile picture URL with fallback to placeholder.
     */
    public function getProfilePictureUrlAttribute(): string
    {
        return $this->profile_picture ? asset('storage/' . $this->profile_picture) : asset('images/avatar-placeholder.svg');
    }

    /**
     * Get the sneezes for the user.
     */
    public function sneezes(): HasMany
    {
        return $this->hasMany(Sneeze::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPassword($token));
    }
}
