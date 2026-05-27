<?php

namespace App\Services;

use Illuminate\Contracts\Hashing\Hasher;

class Pbkdf2Hasher implements Hasher
{
    /**
     * Get information about the given hashed value.
     *
     * @param  string  $hashedValue
     * @return array
     */
    public function info($hashedValue)
    {
        return [
            'algo' => str_starts_with($hashedValue, 'pbkdf2:') ? 'pbkdf2' : 'bcrypt',
            'algoName' => str_starts_with($hashedValue, 'pbkdf2:') ? 'pbkdf2' : 'bcrypt',
            'options' => [],
        ];
    }

    /**
     * Hash the given value.
     *
     * @param  string  $value
     * @param  array  $options
     * @return string
     */
    public function make($value, array $options = [])
    {
        // Generate a secure 16-byte random salt
        $saltBytes = random_bytes(16);
        $saltBase64 = base64_encode($saltBytes);

        // Compute PBKDF2 hash using SHA-1, 10000 iterations, 20 bytes output
        $hashBytes = hash_pbkdf2('sha1', $value, $saltBytes, 10000, 20, true);
        $hashBase64 = base64_encode($hashBytes);

        return "pbkdf2:{$saltBase64}:{$hashBase64}";
    }

    /**
     * Check the given plain value against a hash.
     *
     * @param  string  $value
     * @param  string  $hashedValue
     * @param  array  $options
     * @return bool
     */
    public function check($value, $hashedValue, array $options = [])
    {
        if (str_starts_with($hashedValue, 'pbkdf2:')) {
            $parts = explode(':', $hashedValue, 3);
            if (count($parts) < 3) {
                return false;
            }
            $salt = $parts[1];
            $hash = $parts[2];

            $saltBytes = base64_decode($salt);
            $computedHashBytes = hash_pbkdf2('sha1', $value, $saltBytes, 10000, 20, true);
            $computedHash = base64_encode($computedHashBytes);

            return hash_equals($hash, $computedHash);
        }

        // Fallback for standard bcrypt hashes
        return password_verify($value, $hashedValue);
    }

    /**
     * Determine if the given hash has been hashed using the given options.
     *
     * @param  string  $hashedValue
     * @param  array  $options
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = [])
    {
        return !str_starts_with($hashedValue, 'pbkdf2:');
    }
}
