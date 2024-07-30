<?php

namespace App\Http\Livewire\Panel\Dashboard;

use Livewire\Component;
use App\Models\Company;
use App\Models\User;

class CardInfo extends Component
{
    public $user;

    public $users_count = 0;
    public $companies_count = 0;
    public $invoices_count = 0;

    public function mount($user)
    {
        $this->user = $user;
        $this->counter();
    }

    public function render()
    {
        return view('livewire.panel.dashboard.card-info');
    }

    protected function counter()
    {
        if ($this->user->is_admin == "S") {
            $this->users_count = User::count();
        }

        $this->companies_count = Company::count();
        $this->invoices_count = Company::withCount('documents')->pluck('documents_count')->sum();
    }
}
