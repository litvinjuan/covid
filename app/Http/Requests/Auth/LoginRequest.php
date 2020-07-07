<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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

    public function credentials(): array
    {
        return [
            'email' => $this->input(self::EMAIL),
            'password' => $this->input(self::PASSWORD),
        ];
    }
}
