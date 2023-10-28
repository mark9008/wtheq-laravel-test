<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     *
     * @param User $user
     * @return void
     */
    public function updated(User $user): void
    {
        // check if avatar is updated to delete old avatar image file from storage
        if ($user->isDirty('avatar')) {
            // get old avatar path
            $old_avatar = $user->getOriginal('avatar');

            // check if old avatar path is not empty
            if (!empty($old_avatar)) {
                // get old avatar path
                $old_avatar_path = public_path('storage/' . $old_avatar);

                // check if old avatar file exists
                if (file_exists($old_avatar_path)) {
                    // delete old avatar file
                    unlink($old_avatar_path);
                }
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param User $user
     * @return void
     */
    public function deleted(User $user): void
    {
       // delete avatar image file from storage
        $avatar = $user->avatar;

        // check if user has avatar
        if (!empty($avatar)) {
            // get avatar path
            $avatar_path = public_path('storage/' . $avatar);
            // check if avatar file exists
            if (file_exists($avatar_path)) {
                // delete avatar file
                unlink($avatar_path);
            }
        }
    }
}
