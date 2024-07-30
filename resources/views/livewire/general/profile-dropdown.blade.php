<div class="profile">
    <div class="dropdown">
        <a href="#">
            <img src="https://t3.ftcdn.net/jpg/05/53/79/60/360_F_553796090_XHrE6R9jwmBJUMo9HKl41hyHJ5gqt9oz.jpg">
        </a>
        <div class="dropdown-menu right">
            <a class="dropdown-item" href="#" data-trigger="modal"
                data-modal="#modal-data-to-user"
                wire:click.prevent="$emitTo('panel.user.modal-data-to-user', 'eventAction', 'edit', {{ Auth::id() }})">
                Alterar senha
            </a>

            <a class="dropdown-item" href="#" wire:click.prevent="logout">Sair</a>
        </div>
    </div>
</div>

@push('modals')
    <livewire:panel.user.modal-data-to-user />
@endpush