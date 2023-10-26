<?php

namespace App\Http\Repositories;

use App\Models\User;
use Carbon\Carbon;

class UserRepository
{
    /**
     * @param string $email
     * @param string $name
     * @param null|string $password
     * @param string|null $country
     * @param bool $MakeVerified
     * @return User
     */
    public function createUser(string $email, string $name, string $password, bool $is_active = true, string $type="normal"): User
    {
        $user = User::firstOrCreate(['email' => $email], [
            'email' => $email,
            'name' => $name,
            'password' => bcrypt($password) ?? bcrypt(time()),
            'is_active' => $is_active,
            'type' => $type,
            'email_verified_at' => Carbon::now(),
        ]);
        return $user;

    }
}
