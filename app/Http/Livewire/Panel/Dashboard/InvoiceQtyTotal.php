<?php

namespace App\Http\Livewire\Panel\Dashboard;

use Livewire\Component;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceQtyTotal extends Component
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
        return view('livewire.panel.dashboard.invoice-qty-total');
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
                            COUNT(id) as qty,
                            CASE model
                                WHEN 55 THEN "NF-e"
                                WHEN 57 THEN "CT-e"
                                WHEN 58 THEN "MDF-e"
                                WHEN 59 THEN "CF-e Sat"
                                WHEN 65 THEN "NFC-e"
                            END AS model,
                            SUM(vNF) AS total
                        ')
            ->where(function ($query) {
                $this->querySearch($query);
            })
            ->whereIn('cnpj_cpf', $this->getCompanies())
            ->groupBy('documents.model')
            ->get()
            ->toArray();

        $this->invoices = json_decode(json_encode($invoices), true);

        $this->emit('eventInitChartQtyTotal', $this->invoices);
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
