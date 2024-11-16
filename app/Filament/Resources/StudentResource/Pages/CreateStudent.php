<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\WelcomeUserMail;
use Illuminate\Support\Facades\Mail;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = User::create([
            'name' => $data['first_name'].' '.$data['last_name'],
            'email' => env('STD_EMAIL_PREFIX').'_'.$data['reg_no'].'@'.env('STD_EMAIL_DOMAIN'),
            'password' => Hash::make(env('STD_DEFAULT_PWD')), // Assuming 'password' is part of the form
        ]);
        // Assign the "student" role to the user
        $user->assignRole('student');
        Mail::to($data['email'])->send(new WelcomeUserMail($user->name, $user->email, $user->password));

        // Add the User ID to the Student data
        $data['user_id'] = $user->id;

        return $data;
    }
}
