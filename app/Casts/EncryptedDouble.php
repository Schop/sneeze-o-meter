<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class EncryptedDouble implements CastsAttributes
{
    /**
     * Cast the stored value to the given type.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return null;
        }
        
        $decrypted = decrypt($value);
        // Unserialize if it's serialized PHP data
        if (is_string($decrypted) && str_starts_with($decrypted, 'd:')) {
            return (float) unserialize($decrypted);
        }
        return (float) $decrypted;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return null;
        }
        
        return encrypt($value);
    }
}
