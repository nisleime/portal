<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email;
    public $password;

    protected $messages = [
        'email.required' => 'Obrigatório.',
        'email.email' => 'E-mail inválido.',
        'password.required' => 'Obrigatório.',
    ];

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.auth');
    }

    public function submit()
    {
        $credentials = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {

            request()->session()->regenerate();

            session()->flash('message-success', "Logado com sucesso.");

            return redirect()->intended(route('panel.dashboard.index'));
        }

        session()->flash('message-warning', "E-mail ou senha inválido.");
    }
}
