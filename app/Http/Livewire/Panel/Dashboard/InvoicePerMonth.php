<?php

namespace App\Http\Livewire\Panel\Dashboard;

use Livewire\Component;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoicePerMonth extends Component
{
    public $user;

    public $invoices = [];

    public $search;

    protected $listeners = ['eventInitAttributes', 'eventDocsPerPeriodSearch'];

    public function mount($user)
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.panel.dashboard.invoice-per-month');
    }

    public function eventInitAttributes()
    {
        $this->getInvoices();
    }

    public function eventDocsPerPeriodSearch($args)
    {
        $this->search = $args;

        $this->getInvoices();
    }

    public function getInvoices()
    {
        DB::statement('SET sql_mode=""');
        DB::statement('SET lc_time_names = "pt_BR"');

        $invoices = DB::table('documents')
            ->selectRaw('
                            DATE_FORMAT(issue_dh, "%b/%y") AS month,
                            SUM(IF(model = 55, 1, 0)) AS "55",
                            SUM(IF(model = 57, 1, 0)) AS "57",
                            SUM(IF(model = 58, 1, 0)) AS "58",
                            SUM(IF(model = 59, 1, 0)) AS "59",
                            SUM(IF(model = 65, 1, 0)) AS "65"
                        ')
            ->where(function ($query) {
                $this->querySearch($query);
            })
            ->whereIn('cnpj_cpf', $this->getCompanies())
            ->orderBy('issue_dh')
            ->groupBy('month')
            ->get()
            ->toArray();

        $this->invoices = json_decode(json_encode($invoices), true);

        $this->emit('eventInitChartQtyPerMonth', $this->invoices);
    }

    public function getCompanies()
    {
        return Company::get()->pluck('cnpj_cpf');
    }

    protected function querySearch($query)
    {
        $this->searchDefault($query);

        if (is_null($this->search)) {
            return;
        }

        $query->when($this->search['first_date'], function ($query, $first_date) {
            return $query->where('issue_dh', '>=', $first_date);
        })->when($this->search['last_date'], function ($query, $last_date) {
            return $query->where('issue_dh', '<=', $last_date);
        });

        $query->when($this->search['related_companies'], function ($query, $related_companies) {
            return $query->whereIn('cnpj_cpf', $related_companies);
        });

        $query->when($this->search['environment_types'], function ($query, $environment_types) {
            return $query->whereIn('environment_type', $environment_types);
        });

        $query->when($this->search['doc_status'], function ($query, $doc_status) {
            return $query->whereIn('status_xml', $doc_status);
        });
    }

    protected function searchDefault($query)
    {
        if (is_null($this->search) || empty($this->search['first_date']) && empty($this->search['last_date'])) {
            $query->where('issue_dh', '>=', Carbon::now()->startOfMonth()->toDateTimeString());
            $query->where('issue_dh', '<=', Carbon::now()->toDateTimeString());
        }
    }
}
