<?php

namespace App\Http\Livewire\Panel\Dashboard;

use Livewire\Component;
use App\Models\Company;
use Carbon\Carbon;

class FilterDocPerPeriod extends Component
{
    public $user;

    public $first_date;
    public $last_date;
    public $related_companies = [];
    public $environment_types = [];
    public $doc_status = [];

    protected $rules = [
        'first_date' => 'required_with:last_date',
        'last_date' => 'required_with:first_date',
        'environment_types' => 'required',
    ];

    protected $messages = [
        'first_date.required_with' => 'Obrigatório',
        'last_date.required_with' => 'Obrigatório',
        'environment_types.required' => 'Obrigatório',
    ];

    public function mount($user)
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.panel.dashboard.filter-doc-per-period', [
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

        $this->emit('eventDocsPerPeriodSearch', $this->searchArgs());
        $this->emit('eventCloseModal', "#modal-filter-doc-per-period");
    }

    public function resetSearch()
    {
        $this->emit('eventDocsPerPeriodSearch', null);
        $this->emit('eventCloseModal', "#modal-filter-doc-per-period");
        $this->reset($this->searchArgs(true));
        $this->resetErrorBag();
    }

    public function searchArgs($keys = false)
    {
        $args = [
            'first_date' => $this->datePtbrToMysql($this->first_date),
            'last_date' => $this->datePtbrToMysql($this->last_date),
            'related_companies' => $this->related_companies,
            'environment_types' => $this->environment_types,
            'doc_status' => $this->doc_status,
        ];

        if ($keys) {
            return array_keys($args);
        }

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
