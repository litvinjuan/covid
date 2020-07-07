<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    const EMAIL = 'email';
    const PASSWORD = 'password';

    public function rules()
    {
        return [
            self::EMAIL => 'required|email',
            self::PASSWORD => 'required|string',
        ];
    }

    public function email(): string
    {
        return $this->input(self::EMAIL);
    }

    public function password(): string
    {
        return $this->input(self::PASSWORD);
    }
}
