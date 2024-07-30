<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Models\Document;
use App\Models\Company;
use App\Models\DisableDocument;
use App\Models\EventDocument;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function invoices()
    {
        // $pdf = App::make('dompdf.wrapper');
        // $pdf->loadView('report.invoices', [
        //     'invoices' => $this->getInvoices(),
        //     'invoicesOverview' => $this->getInvoicesOverview(),
        //     'searchArgsToDocReport' => session('searchArgsToDocReport'),
        // ]);

        // return $pdf->stream();

        return view('report.invoices', [
            'invoices' => $this->getInvoices(),
            'invoicesOverview' => $this->getInvoicesOverview(),
            'searchArgsToDocReport' => session('searchArgsToDocReport'),
        ]);
    }

    public function events()
    {
        return view('report.events', [
            'events' => $this->getEvents(),
            'searchArgsToDocReport' => session('searchArgsToDocReport'),
        ]);
    }

    public function disables()
    {
        return view('report.disables', [
            'disables' => $this->getDisables(),
            'searchArgsToDocReport' => session('searchArgsToDocReport'),
        ]);
    }

    public function getInvoices($ins = null)
    {
        $documents = Document::where(function ($query) {
            $this->querySearchInvoices($query);
        })->whereIn('cnpj_cpf', $this->getCompanies('cnpj_cpf'));

        $documents->when($ins, function ($query, $ins) {
            return $query->whereIn('id', $ins);
        })
        ->orderBy('cnpj_cpf', 'ASC')
        ->orderBy('series', 'ASC')
        ->orderBy('model', 'ASC')
        ->orderBy('number', 'ASC')
        ->orderBy('issue_dh', 'ASC');

        return $documents->get();
    }

    public function getEvents($ins = null)
    {
        $events = EventDocument::where(function ($query) {
            $this->querySearchEvents($query);
        })->whereIn('cnpj', $this->getCompanies('cnpj_cpf'));

        $events->when($ins, function ($query, $ins) {
            return $query->whereIn('id', $ins);
        })
        ->orderBy('cnpj', 'ASC')
        ->orderBy('model', 'ASC')
        ->orderBy('event_number', 'ASC')
        ->orderBy('event_dh', 'ASC');

        return $events->get();
    }

    public function getDisables($ins = null)
    {
        $disables = DisableDocument::where(function ($query) {
            $this->querySearchDisables($query);
        })->whereIn('cnpj', $this->getCompanies('cnpj_cpf'));

        $disables->when($ins, function ($query, $ins) {
            return $query->whereIn('id', $ins);
        })
        ->orderBy('cnpj', 'ASC')
        ->orderBy('series', 'ASC')
        ->orderBy('model', 'ASC')
        ->orderBy('event_dh', 'ASC');

        return $disables->get();
    }

    public function getInvoicesOverview($ins = null)
    {
        DB::statement('SET sql_mode=""');
        DB::statement('SET lc_time_names = "pt_BR"');

        $documents = Document::selectRaw('
            COUNT(id) as qty,
            CASE status_xml
                WHEN 100 THEN "Autorizada"
                WHEN 101 THEN "Cancelada"
                WHEN 150 THEN "Autorizada fora do prazo"
                WHEN 151 THEN "Cancelada fora do prazo"
                WHEN 110 THEN "Uso denegado"
                WHEN 102 THEN "Inutilização de N.º homologado"
                WHEN 135 THEN "Evento registrado"
            END AS status_xml,
            CASE model
                WHEN 55 THEN "NF-e"
                WHEN 57 THEN "CT-e"
                WHEN 58 THEN "MDF-e"
                WHEN 59 THEN "Entrada"
                WHEN 65 THEN "NFC-e"
            END AS model,
            SUM(vNF) AS total
        ')->where(function ($query) {
            $this->querySearchInvoices($query);
        })->whereIn('cnpj_cpf', $this->getCompanies('cnpj_cpf'));

        $documents->when($ins, function ($query, $ins) {
            return $query->whereIn('id', $ins);
        });

        $documents->groupBy('model');

        return $documents->get()->toArray();
    }

    public function getCompanies($pluck)
    {
        return Company::get()->pluck($pluck);
    }

    protected function querySearchInvoices($query)
    {
        $searchArgs = session('searchArgsToDocReport');

        $this->searchDefault($query, $searchArgs);

        if (is_null(session('searchArgsToDocReport'))) {
            return;
        }

        $query->when($searchArgs['first_date'], function ($query, $first_date) {
            return $query->where('issue_dh', '>=', $first_date);
        })->when($searchArgs['last_date'], function ($query, $last_date) {
            return $query->where('issue_dh', '<=', $last_date);
        });

        $query->when($searchArgs['doc_number'], function ($query, $doc_number) {
            return $query->where('number', $doc_number);
        });

        $query->when($searchArgs['protocol_number'], function ($query, $protocol_number) {
            return $query->where('protocol', $protocol_number);
        });

        $query->when($searchArgs['related_companies'], function ($query, $related_companies) {
            return $query->whereIn('cnpj_cpf', $related_companies);
        });

        $query->when($searchArgs['doc_types'], function ($query, $doc_types) {
            return $query->whereIn('model', $doc_types);
        });

        $query->when($searchArgs['environment_types'], function ($query, $environment_types) {
            return $query->whereIn('environment_type', $environment_types);
        });

        $query->when($searchArgs['doc_status'], function ($query, $doc_status) {
            return $query->whereIn('status_xml', $doc_status);
        });
    }

    protected function querySearchEvents($query)
    {
        $searchArgs = session('searchArgsToDocReport');

        $this->searchDefault($query, $searchArgs);

        if (is_null($searchArgs)) {
            return;
        }

        $query->when($searchArgs['first_date'], function ($query, $first_date) {
            return $query->where('event_dh', '>=', $first_date);
        })->when($searchArgs['last_date'], function ($query, $last_date) {
            return $query->where('event_dh', '<=', $last_date);
        });

        $query->when($searchArgs['doc_number'], function ($query, $doc_number) {
            return $query->where('event_number', $doc_number);
        });

        $query->when($searchArgs['protocol_number'], function ($query, $protocol_number) {
            return $query->where('protocol_number', $protocol_number);
        });

        $query->when($searchArgs['related_companies'], function ($query, $related_companies) {
            return $query->whereIn('cnpj', $related_companies);
        });

        $query->when($searchArgs['doc_types'], function ($query, $doc_types) {
            return $query->whereIn('model', $doc_types);
        });

        $query->when($searchArgs['environment_types'], function ($query, $environment_types) {
            return $query->whereIn('environment_type', $environment_types);
        });

        $query->when($searchArgs['doc_status'], function ($query, $doc_status) {
            return $query->whereIn('event_status', $doc_status);
        });
    }

    protected function querySearchDisables($query)
    {
        $searchArgs = session('searchArgsToDocReport');

        $this->searchDefault($query, $searchArgs);

        if (is_null($searchArgs)) {
            return;
        }

        $query->when($searchArgs['first_date'], function ($query, $first_date) {
            return $query->where('event_dh', '>=', $first_date);
        })->when($searchArgs['last_date'], function ($query, $last_date) {
            return $query->where('event_dh', '<=', $last_date);
        });

        $query->when($searchArgs['doc_number'], function ($query, $doc_number) {
            return $query->where('number_start', $doc_number);
        });

        $query->when($searchArgs['protocol_number'], function ($query, $protocol_number) {
            return $query->where('protocol_number', $protocol_number);
        });

        $query->when($searchArgs['related_companies'], function ($query, $related_companies) {
            return $query->whereIn('cnpj', $related_companies);
        });

        $query->when($searchArgs['doc_types'], function ($query, $doc_types) {
            return $query->whereIn('model', $doc_types);
        });

        $query->when($searchArgs['environment_types'], function ($query, $environment_types) {
            return $query->whereIn('environment_type', $environment_types);
        });

        $query->when($searchArgs['doc_status'], function ($query, $doc_status) {
            return $query->whereIn('event_status', $doc_status);
        });
    }

    protected function searchDefault($query, $search)
    {
        $docType = session('docType');

        if (is_null($search) || empty($search['first_date']) && empty($search['last_date'])) {

            if($docType == 'invoice'){
                $query->where('issue_dh', '>=', Carbon::now()->startOfMonth()->toDateTimeString());
                $query->where('issue_dh', '<=', Carbon::now()->toDateTimeString());
            } elseif ($docType == 'event' || $docType == 'disable'){
                $query->where('event_dh', '>=', Carbon::now()->startOfMonth()->toDateTimeString());
                $query->where('event_dh', '<=', Carbon::now()->toDateTimeString());
            }

        }
    }
}
