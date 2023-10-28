<?php

namespace App\Http\Repositories;

use App\Models\User;
use Carbon\Carbon;

class UserRepository
{
    /**
     * User variable to store user model
     * @var User
     */
    protected User $user;

    /**
     * List function to list all users
     * @param bool $active // if true, only active users will be returned
     */
    public function list($active = true)
    {
        $query = User::query();

        if ($active) {
            $query->where('is_active', true);
        }

        return $query->get();
    }


    /**
     * Get function to get user by id
     * @param int $id
     * @return User
     */
    public function get(int $id): User
    {
        if (!isset($this->user)) $this->set($id);
        return $this->user;
    }

    /**
     * Set function to set the repository user by id
     * @param int $id
     * @return User
     */
    public function set(int $id): User
    {
        if (empty($this->user) || $id != $this->user->id)
            $this->user = User::findOrFail($id);
        return $this->user;
    }

    /**
     * Create function to create user with given data
     * @param string $email
     * @param string $name
     * @param string $password
     * @param bool $is_active
     * @param string $type
     * @return User
     */
    public function create(string $email, string $name, string $password, bool $is_active = true, string $type = "normal"): User
    {
        return User::firstOrCreate(['email' => $email], [
            'email' => $email,
            'name' => $name,
            'password' => bcrypt($password) ?? bcrypt(time()),
            'is_active' => $is_active,
            'type' => $type,
            'email_verified_at' => Carbon::now(),
        ]);
    }

    /**
     * Update function to update user by id with given data
     * @param int $id
     * @param array $data
     * @return User
     */
    public function update(int $id, array $data): User
    {
        $this->set($id);
        $this->user->update($data);
        return $this->user;
    }

    /**
     * Delete function to delete user by id
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->set($id);
        return $this->user->update(['is_active' => false]);
    }

    /**
     * Search by User Type
     * @param string $type
     * @param bool $active  // if true, only active users will be returned
     */
    public function searchByType(string $type, bool $active = true)
    {
        $query = User::where('type', '=', $type);
        if ($active)
            $query->where('is_active', '=', true);
        return $query->get();
    }
}
