<?php

namespace App\Actions\Fortify;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'username'   => ['required', 'string', 'max:255', Rule::unique(User::class)],
            'email'      => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password'   => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input) {
            $user = User::create([
                'username' => $input['username'],
                'email'    => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            Profile::create([
                'user_id'    => $user->id,
                'first_name' => $input['first_name'],
                'last_name'  => $input['last_name'],
            ]);

            $user->assignRole('user');

            return $user;
        });
    }
}
