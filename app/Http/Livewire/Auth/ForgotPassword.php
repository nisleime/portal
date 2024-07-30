<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Password;

class ForgotPassword extends Component
{
    public $email;

    protected $messages = [
        'email.required' => 'Obrigatório.',
        'email.email' => 'E-mail inválido.',
    ];

    public function render()
    {
        return view('livewire.auth.forgot-password')->layout('layouts.auth');
    }

    public function submit()
    {
        $credentials = $this->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($credentials);

        if ($status === Password::RESET_LINK_SENT || $status === Password::RESET_THROTTLED) {

            $msg = "Verifique a caixa de entrada ou spam do e-mail {$this->email}, para criar a nova senha.";

            session()->flash('message-success', $msg);

            $this->emit('eventCuteToast', $msg, 200);
            $this->reset('email');

            return;
        }

        session()->flash('message-warning', "Verifique sua identidade digitando o e-mail de usuário associado à sua conta.");
    }
}
