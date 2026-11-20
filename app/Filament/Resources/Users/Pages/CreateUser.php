<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        /** @var User $user */
        $user = parent::handleRecordCreation($data);
        $user->assignRole('admin');

        return $user;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['email_verified'] = Carbon::now();
        $data['password'] = bcrypt($data['password']);

        return $data;
    }
}
