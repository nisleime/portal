<?php

namespace App\Http\Livewire\Panel\Company;

use Livewire\Component;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use App\Helpers\Mask;

class ModalDataToCompany extends Component
{
    public $company_id;
    public $cnpj_cpf;
    public $corporate_name;
    public $fantasy_name;
    public $email;
    public $phone_number;
    public $public_place;
    public $home_number;
    public $complement;
    public $district;
    public $zip_code;
    public $county;
    public $uf;

    public $related_users;

    public $action;

    protected $listeners = ['eventAction'];

    protected $messages = [
        'corporate_name.required' => 'Obrigatório.',
        'cnpj_cpf.required' => 'Obrigatório.',
    ];

    public function render()
    {
        return view('livewire.panel.company.modal-data-to-company', [
            'users' => DB::table('users')->select('id', 'name')->get(),
        ]);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->rulesCompany());
    }

    public function eventAction($action, $company_id = null)
    {
        $this->action = $action;

        $this->resetCompany();

        if ($company_id) {
            $this->company_id = $company_id;
            $this->edit();
        }
    }

    public function submit()
    {
        $this->updateOrCreate();
    }

    protected function updateOrCreate()
    {
        $this->validate($this->rulesCompany());

        $data = [
            'corporate_name' => $this->corporate_name,
            'fantasy_name' => $this->fantasy_name,
            'cnpj_cpf' => preg_replace("/\D/", "", $this->cnpj_cpf),
            'email' => $this->email,
            'phone_number' => preg_replace("/\D/", "", $this->phone_number),
            'public_place' => $this->public_place,
            'home_number' => $this->home_number,
            'complement' => $this->complement,
            'district' => $this->district,
            'zip_code' => $this->zip_code,
            'county' => $this->county,
            'uf' => $this->uf,
        ];

        try {
            $company = Company::updateOrCreate(['id' => $this->company_id], $data);
            $company->users()->sync($this->related_users);

            if ($this->action == 'store') {
                $this->resetCompany();
            }

            $this->emitTo('panel.company.index', '$refresh');
            $this->emit('eventCloseModal', "#modal-data-to-company");
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
        $company = Company::find($this->company_id);

        if ($company) {
            $this->corporate_name = $company['corporate_name'];
            $this->fantasy_name = $company['fantasy_name'];
            $this->cnpj_cpf = Mask::run($company['cnpj_cpf'], strlen($company['cnpj_cpf']) > 11 ? '##.###.###/####-##' : '###.###.###-##');
            $this->email = $company['email'];
            $this->phone_number =  Mask::run($company['phone_number'], strlen($company['phone_number']) > 10 ? '(##) #####-####' : '(##) ####-####');
            $this->public_place = $company['public_place'];
            $this->home_number = $company['home_number'];
            $this->complement = $company['complement'];
            $this->district = $company['district'];
            $this->zip_code = $company['zip_code'];
            $this->county = $company['county'];
            $this->uf = $company['uf'];
            $this->related_users = $company->users()->pluck('id');
        }
    }

    protected function resetCompany()
    {
        $this->reset([
            'company_id',
            'corporate_name',
            'fantasy_name',
            'cnpj_cpf',
            'email',
            'phone_number',
            'public_place',
            'home_number',
            'complement',
            'district',
            'zip_code',
            'county',
            'uf',
            'related_users'
        ]);
    }

    protected function rulesCompany()
    {
        $rules = [
            'corporate_name' => 'required',
            'cnpj_cpf' => 'required',
        ];

        return $rules;
    }
}
