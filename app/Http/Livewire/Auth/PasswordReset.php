<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordReset extends Component
{
    public $token;
    public $email;
    public $password;
    public $password_confirmation;

    protected $messages = [
        'email.required' => 'Obrigatório.',
        'email.email' => 'E-mail inválido.',
        'password.required' => 'Obrigatório.',
        'password.min' => 'A senha de deve ter no mínimo 8 caracteres.',
        'password.confirmed' => 'A senha de confirmação não é igual.',
    ];

    public function mount($token)
    {
        $this->token = $token;
    }

    public function render()
    {
        return view('livewire.auth.password-reset', [
            'token' => $this->token
        ])->layout('layouts.auth');
    }

    public function submit()
    {
        $credentials = $this->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $credentials['password_confirmation'] = $this->password_confirmation;

        $status = Password::reset($credentials, function ($user, $password) {

            $user->forceFill([
                'password' => bcrypt($password)
            ])->setRememberToken(Str::random(60));

            $user->save();
        });

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('auth.login')->with('message-password-reseted', 'Pronto, entre com a nova senha.');
        } elseif ($status === Password::INVALID_TOKEN) {
            session()->flash('message-error', 'O token temporário está expirado, <a class="text-black" href="' . route('auth.forgot.password') . '">gere um novo</a>.');
        } else {
            session()->flash('message-error', "Não foi possivel criar a nova senha.");
        }
    }
}
