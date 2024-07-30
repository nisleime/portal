<?php

namespace App\Http\Livewire\Panel\Dashboard;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DisableDocument;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use ZipArchive;

class Disable extends Component
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
        return view('livewire.panel.dashboard.disable', [
            'disables' => $this->getDisables(true)
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
        $documents = $this->getDisables(false, $this->checkedDocIds());

        if ($documents->isEmpty()) {
            $this->emit('eventCuteToast', "Nenhuma documento disponível para download!", 400);
            return;
        }

        $zip = new ZipArchive();

        $time = time();
        $name = "disabled-{$this->user->id}-{$time}.zip";
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

            $path_download = str_replace('/docs/eventos/', '', $document->path_xml);

            if (File::exists(storage_path("app{$document->path_xml}"))) {
                $zip->addFile(storage_path("app{$document->path_xml}"), $path_download);
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
        $disables = $this->getDisables()->pluck('id')->toArray();

        if ($checked) {
            foreach ($disables as $disable) {
                $this->check_doc[intval($disable)] = true;
            }
        } else {
            foreach ($disables as $disable) {
                $this->check_doc[intval($disable)] = false;
            }
        }

        $this->check_all_docs = $checked;
    }

    public function getDisables($paginate = false, $ins = null)
    {
        $disables = DisableDocument::where(function ($query) {
            $this->querySearch($query);
        })->whereIn('cnpj', $this->getCompanies());

        $disables->when($ins, function ($query, $ins) {
            return $query->whereIn('id', $ins);
        })
        ->orderBy('cnpj', 'ASC')
        ->orderBy('series', 'ASC')
        ->orderBy('model', 'ASC')
        ->orderBy('event_dh', 'ASC');

        if ($paginate) {
            return $disables->paginate(env('PAGINATION_LIMIT', 5));
        }

        return $disables->get();
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
        $document = DB::table('disable_documents')->where('id', $id)->first();

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
            return $query->where('event_dh', '>=', $first_date);
        })->when($this->search['last_date'], function ($query, $last_date) {
            return $query->where('event_dh', '<=', $last_date);
        });

        $query->when($this->search['doc_number'], function ($query, $doc_number) {
            return $query->where('number_start', $doc_number);
        });

        $query->when($this->search['protocol_number'], function ($query, $protocol_number) {
            return $query->where('protocol_number', $protocol_number);
        });

        $query->when($this->search['related_companies'], function ($query, $related_companies) {
            return $query->whereIn('cnpj', $related_companies);
        });

        $query->when($this->search['doc_types'], function ($query, $doc_types) {
            return $query->whereIn('model', $doc_types);
        });

        $query->when($this->search['environment_types'], function ($query, $environment_types) {
            return $query->whereIn('environment_type', $environment_types);
        });

        $query->when($this->search['doc_status'], function ($query, $doc_status) {
            return $query->whereIn('event_status', $doc_status);
        });
    }

    protected function searchDefault($query)
    {
        if (is_null($this->search) || empty($this->search['first_date']) && empty($this->search['last_date'])) {
            $query->where('event_dh', '>=', Carbon::now()->startOfMonth()->toDateTimeString());
            $query->where('event_dh', '<=', Carbon::now()->toDateTimeString());
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
