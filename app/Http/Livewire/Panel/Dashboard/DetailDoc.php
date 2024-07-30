<?php

namespace App\Http\Livewire\Panel\Dashboard;

use Livewire\Component;
use App\Models\Document;
use App\Models\EventDocument;
use App\Models\DisableDocument;
use Illuminate\Support\Facades\Gate;

class DetailDoc extends Component
{
    public $user;

    public $details = [];

    protected $listeners = ['eventDetailDoc'];

    public function mount($user)
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.panel.dashboard.detail-doc');
    }

    public function eventDetailDoc($id, $type)
    {
        if ($type == 'invoice') {
            $this->detailInvoice($id);
        } elseif ($type == 'event') {
            $this->detailEvent($id);
        } elseif ($type == 'disable') {
            $this->detailDisable($id);
        }
    }

    public function detailInvoice($id)
    {
        $document = Document::find($id);

        $policy = Gate::inspect('access-invoice', $document);

        if ($policy->denied()) {
            return;
        }

        $this->details = Document::find($id);

        $this->emit('eventOpenModal', "#modal-detail-doc");
    }

    public function detailEvent($id)
    {
        $this->details = EventDocument::find($id);

        $this->emit('eventOpenModal', "#modal-detail-doc");
    }

    public function detailDisable($id)
    {
        $this->details = DisableDocument::find($id);

        $this->emit('eventOpenModal', "#modal-detail-doc");
    }
}
