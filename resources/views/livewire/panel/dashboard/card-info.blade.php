<div>

    @if ($user->is_admin == 'S')

        <div class="col-33 mt-30">

            <div class="dash-info-icon ocean big">
                <div class="col">
                    <span class="big-text">{{ $users_count }}</span>
                    <span class="small-text">Usuários</span>
                </div>
                <div class="col">
                    <span class="icon">
                        <i class="fas fa-users"></i>
                    </span>
                </div>
            </div>

        </div>

        <div class="{{ $user->is_admin == 'S' ? 'col-33' : 'col-50' }} mt-30">

            <div class="dash-info-icon red big">
                <div class="col">
                    <span class="big-text">{{ $companies_count }}</span>
                    <span class="small-text">Empresas</span>
                </div>
                <div class="col">
                    <span class="icon">
                        <i class="far fa-building"></i>
                    </span>
                </div>
            </div>

        </div>

        <div class="{{ $user->is_admin == 'S' ? 'col-33' : 'col-50' }} mt-30">

            <div class="dash-info-icon green big">
                <div class="col">
                    <span class="big-text">{{ $invoices_count }}</span>
                    <span class="small-text">Notas Emitidas</span>
                </div>
                <div class="col">
                    <span class="icon">
                        <i class="fas fa-file-invoice"></i>
                    </span>
                </div>
            </div>

        </div>

    @endif

</div>
