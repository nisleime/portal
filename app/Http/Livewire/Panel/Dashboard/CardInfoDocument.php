<?php

namespace App\Http\Livewire\Panel\Dashboard;

use Livewire\Component;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CardInfoDocument extends Component
{
    public $user;

    public $total_nfe = 0;
    public $total_nfce = 0;
    public $total_cte = 0;
    public $total_mdfe = 0;
    public $total_cfesat = 0;

    public $qty_nfe = 0;
    public $qty_nfce = 0;
    public $qty_cte = 0;
    public $qty_mdfe = 0;
    public $qty_cfesat = 0;

    public $search;

    protected $listeners = ['eventDocsSearch'];

    public function mount($user)
    {
        $this->user = $user;
    }

    public function render()
    {
        $this->getTotals();

        return view('livewire.panel.dashboard.card-info-document');
    }

    public function eventDocsSearch($args)
    {
        $this->search = $args;

        $this->reset([
            'total_nfe',
            'total_cte',
            'total_mdfe',
            'total_cfesat',
            'total_nfce',

            'qty_nfe',
            'qty_cte',
            'qty_mdfe',
            'qty_cfesat',
            'qty_nfce',

        ]);
        $this->getTotals();
    }

    public function getTotals()
    {
        DB::statement('SET sql_mode=""');
        DB::statement('SET lc_time_names = "pt_BR"');

        $documents = DB::table('documents')
            ->selectRaw('
                model,
                COUNT(id) AS qty,
                SUM(vNF) as total
            ')
            ->where(function ($query) {
                $this->querySearch($query);
            })
            ->whereIn('cnpj_cpf', $this->getCompanies())
            ->orderBy('model')
            ->groupBy('documents.model')
            ->get();

        if ($documents->isEmpty()) {
            return;
        }

        foreach ($documents as $doc) {

            switch ($doc->model) {
                case "55":
                    $this->total_nfe = $doc->total;
                    $this->qty_nfe = $doc->qty;
                    break;

                case "57":
                    $this->total_cte = $doc->total;
                    $this->qty_cte = $doc->qty;
                    break;

                case "58":
                    $this->total_mdfe = $doc->total;
                    $this->qty_mdfe = $doc->qty;
                    break;

                case "59":
                    $this->total_cfesat = $doc->total;
                    $this->qty_cfesat = $doc->qty;
                    break;

                case "65":
                    $this->total_nfce = $doc->total;
                    $this->qty_nfce = $doc->qty;
                    break;
            }
        }
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

        $query->when($this->search['doc_number'], function ($query, $doc_number) {
            return $query->where('number', $doc_number);
        });

        $query->when($this->search['protocol_number'], function ($query, $protocol_number) {
            return $query->where('protocol', $protocol_number);
        });

        $query->when($this->search['related_companies'], function ($query, $related_companies) {
            return $query->whereIn('cnpj_cpf', $related_companies);
        });

        $query->when($this->search['doc_types'], function ($query, $doc_types) {
            return $query->whereIn('model', $doc_types);
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
