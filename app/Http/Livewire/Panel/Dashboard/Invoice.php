<?php

namespace App\Http\Livewire\Panel\Dashboard;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Document;
use App\Models\Company;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use ZipArchive;

class Invoice extends Component
{
    use WithPagination;

    public $user;

    public $check_all_docs;
    public $check_doc;

    public $search;

    protected $listeners = ['eventDocsSearch', 'eventDownloadCompressedDoc'];

    public function mount($user)
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.panel.dashboard.invoice', [
            'invoices' => $this->getInvoices(true)
        ]);
    }

    public function getQueryString()
    {
        return [];
    }

    public function paginationView()
    {
        return 'layouts.pagination';
    }

    public function eventDocsSearch($args)
    {
        $this->search = $args;

        $this->resetPage();
        $this->reset(['check_all_docs', 'check_doc']);
    }

    public function eventDownloadCompressedDoc()
    {
        $documents = $this->getInvoices(false, $this->checkedDocIds());

        if ($documents->isEmpty()) {
            $this->emit('eventCuteToast', "Nenhuma nota disponível para download!", 400);
            return;
        }

        $zip = new ZipArchive();

        $time = time();
        $name = "invoice-{$this->user->id}-{$time}.zip";
        $path = storage_path("app/downloads");
        $file = "{$path}/{$name}";

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        if ($zip->open($file, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== true) {
            $this->emit('eventCuteToast', 'Opss, não foi possível compactar o download', 500);
            return;
        }

        foreach ($documents as $document) {

            $policy = Gate::inspect('access-invoice', $document);

            if ($policy->denied()) {
                continue;
            }

            switch ($document->status_xml) {
                case 100:
                    $status = 'Autorizadas';
                    break;
                case 101:
                    $status = 'Canceladas';
                    break;
                case 150:
                    $status = 'Autorizadas fora do prazo';
                    break;
                case 151:
                    $status = 'Canceladas fora do prazo';
                    break;
                case 110:
                    $status = 'Denegadas';
                    break;
            }

            switch ($document->model) {
                case 55:
                    $model = 'NF-e';
                    break;
                case 57:
                    $model = 'CT-e';
                    break;
                case 58:
                    $model = 'MDF-e';
                    break;
                case 59:
                    $model = 'CF-e Sat';
                    break;
                case 65:
                    $model = 'NFC-e';
                    break;
            }
			$path_download = "{$document->cnpj_cpf}/{$model}/{$status}/{$document->key}";
            //$path_download = "{$document->cnpj_cpf}/{$document->ie}/{$model}/{$status}/{$document->month_year}/{$document->key}";

            if (File::exists(storage_path("app{$document->path_xml}"))) {
                $zip->addFile(storage_path("app{$document->path_xml}"), "{$path_download}.xml");
            }
        }

        $zip->close();

        if (File::exists($file)) {
            return response()->download($file, $name, [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend();
        } else {
            $this->emit('eventCuteToast', "Não existe nenhuma nota compactada para download.", 404);
            return;
        }
    }

    public function updatingCheckDoc($checked, $id)
    {
        if (!$checked) {
            $this->check_all_docs = false;
        }

        $this->check_doc[intval($id)] = $checked;
    }

    public function updatingCheckAllDocs($checked)
    {
        $invoices = $this->getInvoices()->pluck('id')->toArray();

        if ($checked) {
            foreach ($invoices as $invoice) {
                $this->check_doc[intval($invoice)] = true;
            }
        } else {
            foreach ($invoices as $invoice) {
                $this->check_doc[intval($invoice)] = false;
            }
        }

        $this->check_all_docs = $checked;
    }

    public function getInvoices($paginate = false, $ins = null)
    {
        $documents = Document::where(function ($query) {
            $this->querySearch($query);
        })->whereIn('cnpj_cpf', $this->getCompanies());

        $documents->when($ins, function ($query, $ins) {
            return $query->whereIn('id', $ins);
        })
        ->orderBy('cnpj_cpf', 'ASC')
        ->orderBy('series', 'ASC')
        ->orderBy('model', 'ASC')
        ->orderBy('number', 'ASC')
        ->orderBy('issue_dh', 'ASC');

        if ($paginate) {
            return $documents->paginate(env('PAGINATION_LIMIT', 5));
        }

        return $documents->get();
    }

    public function getCompanies()
    {
        return Company::get()->pluck('cnpj_cpf');
    }

    public function downloadDocById($id, $type)
    {
        if ($type == 'xml') {
            return $this->downloadDocXmlById($id);
        }
    }

    protected function downloadDocXmlById($id)
    {
        $document = Document::where('id', $id)->first();
        $policy = Gate::inspect('access-invoice', $document);

        if ($policy->denied()) {
            $this->emit('eventCuteToast', "Não autorizado.", 403);
            return;
        }

        $file = storage_path("app{$document->path_xml}");

        if (File::exists($file)) {
            return response()->download($file, null, [
                'Content-Type' => 'application/xml',
            ]);
        } else {
            $this->emit('eventCuteToast', "Não existe o arquivo para download.", 404);
            return;
        }
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

    protected function checkedDocIds()
    {
        $ids = [];

        if (is_null($this->check_doc)) {
            return;
        }

        foreach ($this->check_doc as $key => $value) {
            if ($value) {
                array_push($ids, $key);
            }
        }

        return $ids;
    }
}
