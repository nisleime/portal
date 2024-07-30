<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Document;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function accessInvoice(User $user, Document $document)
    {
        if ($user->is_admin == "N") {
            if ($user->companies->where('cnpj_cpf', $document->cnpj_cpf)->first() == null) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
}
