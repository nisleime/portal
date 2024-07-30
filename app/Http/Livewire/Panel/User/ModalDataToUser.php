<?php

namespace App\Http\Livewire\Panel\User;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ModalDataToUser extends Component
{
    public $user_id;
    public $name;
    public $email;
    public $password;
    public $is_admin;

    public $related_companies;

    public $action;

    protected $listeners = ['eventAction'];

    protected $messages = [
        'name.required' => 'Obrigatório.',
        'password.required' => 'Obrigatório.',
        'is_admin.required' => 'Obrigatório.',
        'email.required' => 'Obrigatório.',
        'email.email' => 'E-mail inválido.',
    ];

    public function render()
    {
        return view('livewire.panel.user.modal-data-to-user', [
            'companies' => DB::table('companies')->select('id', 'corporate_name', 'fantasy_name')->get(),
        ]);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->rulesUser());
    }

    public function eventAction($action, $user_id = null)
    {
        $this->action = $action;

        $this->resetUser();

        if ($user_id) {
            $this->user_id = $user_id;
            $this->edit();
        }
    }

    public function submit()
    {
        $this->updateOrCreate();
    }

    protected function updateOrCreate()
    {
        $this->validate($this->rulesUser());

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => $this->is_admin,
        ];

        if ($this->action == 'edit') {
            if (!is_null($this->password)) {
                $data['password'] = bcrypt($this->password);
            }
        } else {
            $data['password'] = bcrypt($this->password);
        }

        try {
            $user = User::updateOrCreate(['id' => $this->user_id], $data);
            $user->companies()->sync($this->related_companies);

            if ($this->action == 'store') {
                $this->resetUser();
            }

            $this->emitTo('panel.user.index', '$refresh');
            $this->emit('eventCloseModal', "#modal-data-to-user");
            $this->emit('eventCuteToast', $this->action == 'edit' ? "Atualizado com sucesso." : "Cadastrado com sucesso.", 200);
        } catch (\Exception $e) {

            $errorDetails = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ];

            switch ($e->getCode()) {
                case "23000":
                    $this->emit('eventCuteToast', "Verifique os dados, pois alguns já estão cadastrados.", 23000, $errorDetails);
                    break;

                default:
                    $this->emit('eventCuteToast', "Não foi possível salvar.", 500, $errorDetails);
                    break;
            }
        }
    }

    protected function edit()
    {
        $user = User::find($this->user_id);

        if ($user) {
            $this->name = $user['name'];
            $this->email = $user['email'];
            $this->is_admin = $user['is_admin'];
            $this->related_companies = $user->companies()->pluck('id');
        }
    }

    protected function resetUser()
    {
        $this->reset(['user_id', 'name', 'email', 'password', 'is_admin', 'related_companies']);
    }

    protected function rulesUser()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'is_admin' => 'required',
        ];

        if ($this->action == 'store') {
            $rules['password'] = 'required';
        }

        return $rules;
    }
}
