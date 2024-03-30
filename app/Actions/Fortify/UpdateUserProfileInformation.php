<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'login_id' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateProfileInformation');



        if ($input['login_id'] !== $user->login_id) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'login_id' => $input['login_id'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'login_id' => $input['login_id'],
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
