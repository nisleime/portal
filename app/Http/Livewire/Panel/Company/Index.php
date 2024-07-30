<?php

namespace App\Http\Livewire\Panel\Company;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Company;

class Index extends Component
{
    use WithPagination;

    public $user;

    protected $listeners = ['$refresh'];

    public function mount()
    {
        $this->user = auth('web')->user();

        if ($this->user->is_admin == "N") {
            abort(403, 'Unauthorized action.');
        }
    }

    public function render()
    {
        return view('livewire.panel.company.index', [
            'companies' => Company::withCount('users')->withoutGlobalScope('linked_user')->paginate(env('PAGINATION_LIMIT', 5), ['id', 'cnpj_cpf', 'corporate_name'])
        ]);
    }

    public function paginationView()
    {
        return 'layouts.pagination';
    }

    public function deleteCompany($id)
    {
        try {
            $company = Company::find($id);
            $company->delete();

            $this->emitSelf('painel.company.index', '$refresh');
            $this->emit('eventCuteToast', "Excluído com sucesso", 200);
        } catch (\Exception $e) {

            $errorDetails = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ];

            switch ($e->getCode()) {
                case "23000":
                    $this->emit('eventCuteToast', "Não foi possível deletar, pois existem dados relacionados.", 23000, $errorDetails);
                    break;

                default:
                    $this->emit('eventCuteToast', "Não foi possível deletar.", 500, $errorDetails);
                    break;
            }
        }
    }
}
