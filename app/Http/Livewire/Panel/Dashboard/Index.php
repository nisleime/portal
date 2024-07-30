<?php

namespace App\Http\Livewire\Panel\Dashboard;

use Livewire\Component;

class Index extends Component
{
    public $user;

    public $doc_type = 'invoice';

    public $docs_per_period_search;
    public $docs_search;

    public $reportRoute;

    protected $listeners = ['eventDocType', 'eventDocsPerPeriodSearch', 'eventDocsSearch', 'eventDownloadCompressed'];

    public function mount()
    {
        $this->user = auth('web')->user();

        $this->setReportRoute();
        $this->setSessionDocType();

        // remover dados de sessao usados para relatorio de documentos, eventos e inutilizadas
        session()->forget('searchArgsToDocReport');
    }

    public function render()
    {
        return view('livewire.panel.dashboard.index');
    }

    public function eventDocType($type)
    {
        $this->doc_type = $type;

        $this->setReportRoute();
        $this->setSessionDocType();
    }

    public function eventDocsSearch($args)
    {
        $this->docs_search = $args;
    }

    public function eventDocsPerPeriodSearch($args)
    {
        $this->docs_per_period_search = $args;
    }

    public function eventDownloadCompressed($doc_type)
    {
        switch ($doc_type) {
            case 'invoice':
                $this->emitTo('panel.dashboard.invoice', 'eventDownloadCompressedDoc');
                break;

            case 'event':
                $this->emitTo('panel.dashboard.event', 'eventDownloadCompressedDoc');
                break;

            case 'disable':
                $this->emitTo('panel.dashboard.disable', 'eventDownloadCompressedDoc');
                break;
        }
    }

    protected function setSessionDocType()
    {
        session()->put('docType', $this->doc_type);
    }

    protected function setReportRoute()
    {
        if($this->doc_type == 'invoice') {
            $this->reportRoute =  route('panel.reports.invoices');
        } elseif ($this->doc_type == 'event') {
            $this->reportRoute =  route('panel.reports.events');
        } elseif ($this->doc_type == 'disable') {
            $this->reportRoute =  route('panel.reports.disables');
        }
    }
}
