<?php

namespace App\Http\Livewire\Panel\Dashboard;

use Livewire\Component;
use App\Models\Company;
use Carbon\Carbon;

class FilterDoc extends Component
{
    public $user;

    public $first_date;
    public $last_date;
    public $doc_number;
    public $protocol_number;
    public $related_companies = [];
    public $environment_types = [];
    public $doc_types = [];
    public $doc_status = [];

    protected $rules = [
        'doc_number' => 'numeric|nullable',
        'protocol_number' => 'numeric|nullable',
        'first_date' => 'required_with:last_date|nullable',
        'last_date' => 'required_with:first_date|nullable',
        'environment_types' => 'required',
    ];

    protected $messages = [
        'doc_number.numeric' => 'Apenas números',
        'protocol_number.numeric' => 'Apenas números',
        'first_date.required_with' => 'Obrigatório',
        'last_date.required_with' => 'Obrigatório',
        'environment_types.required' => 'Obrigatório',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($user)
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.panel.dashboard.filter-doc', [
            'companies' => $this->getCompanies(),
        ]);
    }

    public function getCompanies()
    {
        return Company::get();
    }

    public function submit()
    {
        $this->validate();

        $this->emit('eventDocsSearch', $this->searchArgs());
        $this->emit('eventCloseModal', "#modal-filter-doc");
    }

    public function resetSearch()
    {
        $this->emit('eventDocsSearch', null);
        $this->emit('eventCloseModal', "#modal-filter-doc");
        $this->reset($this->searchArgs(true));
        $this->resetErrorBag();

        // remover dados de sessao usados para relatorio de documentos, eventos e inutilizadas
        session()->forget('searchArgsToDocReport');
    }

    public function searchArgs($keys = false)
    {
        $args = [
            'first_date' => $this->datePtbrToMysql($this->first_date),
            'last_date' => $this->datePtbrToMysql($this->last_date),
            'doc_number' => $this->doc_number,
            'protocol_number' => $this->protocol_number,
            'related_companies' => $this->related_companies,
            'doc_types' => $this->doc_types,
            'environment_types' => $this->environment_types,
            'doc_status' => $this->doc_status,
        ];

        if ($keys) {
            return array_keys($args);
        }

        // dados de sessao usados para relatorio de documentos, eventos e inutilizadas
        session()->put('searchArgsToDocReport', $args);

        return $args;
    }

    public function datePtbrToMysql($date)
    {
        if (is_null($date) || empty($date)) {
            return;
        }

        return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
    }
}
