<div>

    <!-- modal form -->
    <div wire:ignore.self class="modal-main" id="modal-data-to-user">

        <div class="dialog">

            <div class="content">

                <a href="#" class="close">x</a>

                <div class="header">
                    <p>{{ $action == 'edit' ? 'Editar usuário' : 'Cadastro de usuário' }}</p>
                </div>

                <div class="body">

                    <div class="form-wrap row pt-30 pb-15">

                        <div class="group mb-15 col-50">
                            <label>Name</label>
                            <input type="text" wire:model.defer="name">
                            @error('name') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="group mb-15 col-50">
                            <label>E-mail</label>
                            <input type="email" wire:model.defer="email">
                            @error('email') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="group mb-15 col-50">
                            <label>Administrador</label>
                            <select id="is_admin" class="select-two-modal-user" wire:model="is_admin">
                                <option></option>
                                <option value="S">Sim</option>
                                <option value="N">Não</option>
                            </select>
                            @error('is_admin') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="group mb-15 col-50">
                            <label>Senha</label>
                            <input type="password" wire:model.defer="password">
                            @error('password') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="group mb-15 col-100">
                            <label>Empresas vinculadas</label>
                            <select id="related_companies" class="select-two-modal-user"
                                wire:model.defer="related_companies" multiple>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">
                                        @if ($company->fantasy_name)
                                            {{ Str::upper($company->fantasy_name) }}
                                        @else
                                            {{ Str::upper($company->corporate_name) }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                </div>

                <div class="footer">

                    <div class="row">
                        <div class="col-100">
                            <a href="#" class="btn btn-dark-gray btn-block" wire:click.defer="submit">
                                <i class="far fa-paper-plane"></i>
                                Salvar
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

@push('component-scripts')

    <script>
        document.addEventListener('livewire:load', function() {

            (function($) {

                $.select2ModalUser = function() {
                    $('.select-two-modal-user').select2({
                        language: "pt-BR",
                        placeholder: "---",
                        allowClear: true,
                    });
                };

                $("#is_admin").on('change', function(e) {
                    @this.is_admin = $(this).val();
                });

                $("#related_companies").on('change', function(e) {
                    @this.related_companies = $(this).val();
                });

                $.select2ModalUser();

                Livewire.hook('message.processed', (message, component) => {
                    $.select2ModalUser();
                });

            })(jQuery);

        });
    </script>

@endpush
