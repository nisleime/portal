<div>

    <!-- modal form -->
    <div wire:ignore.self class="modal-main" id="modal-data-to-company">

        <div class="dialog">

            <div class="content modal-medium">

                <a href="#" class="close">x</a>

                <div class="header">
                    <p>{{ $action == 'edit' ? 'Editar empresa' : 'Cadastro de empresa' }}</p>
                </div>

                <div class="body">

                    <div class="form-wrap row pt-30 pb-15">

                        <div class="col-100 mb-30">
                            <div class="box-heading">
                                <h3>Dados gerais</h3>
                            </div>
                        </div>

                        <div class="group mb-15 col-60">
                            <label>Razão social</label>
                            <input type="text" wire:model.defer="corporate_name">
                            @error('corporate_name') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="group mb-15 col-40">
                            <label>CNPJ/CPF</label>
                            <input type="text" class="mask-cnpj_cpf" wire:model.defer="cnpj_cpf">
                            @error('cnpj_cpf') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="group mb-15 col-100">
                            <label>Nome fantasia</label>
                            <input type="text" wire:model.defer="fantasy_name">
                            @error('fantasy_name') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-100 mt-15 mb-30">
                            <div class="box-heading">
                                <h3>Endereço</h3>
                            </div>
                        </div>

                        <div class="group mb-15 col-100">
                            <label>Logradouro</label>
                            <input type="text" wire:model.defer="public_place">
                            @error('public_place') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="group mb-15 col-20">
                            <label>CEP</label>
                            <input type="text" wire:model.defer="zip_code">
                            @error('zip_code') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="group mb-15 col-20">
                            <label>Número</label>
                            <input type="text" wire:model.defer="home_number">
                            @error('home_number') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="group mb-15 col-60">
                            <label>Complemento</label>
                            <input type="text" wire:model.defer="complement">
                            @error('complement') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="group mb-15 col-40">
                            <label>Bairro</label>
                            <input type="text" wire:model.defer="district">
                            @error('district') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="group mb-15 col-20">
                            <label>UF</label>
                            <input type="text" wire:model.defer="uf">
                            @error('uf') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="group mb-15 col-40">
                            <label>Municipio</label>
                            <input type="text" wire:model.defer="county">
                            @error('county') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-100 mt-15 mb-30">
                            <div class="box-heading">
                                <h3>Contato</h3>
                            </div>
                        </div>

                        <div class="group mb-15 col-50">
                            <label>E-mail</label>
                            <input type="text" wire:model.defer="email">
                            @error('email') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="group mb-15 col-50">
                            <label>Telefone</label>
                            <input type="text" class="mask-phone" wire:model.defer="phone_number">
                            @error('phone_number') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="group mb-15 col-100">
                            <label>Usuários vinculados</label>
                            <select id="related_users" class="select-two-modal-company" wire:model.defer="related_users" multiple>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ Str::upper($user->name) }}</option>
                                @endforeach
                            </select>
                            @error('related_users') <span class="error">{{ $message }}</span> @enderror
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

                $.select2ModalCompany = function() {
                    $('.select-two-modal-company').select2({
                        language: "pt-BR",
                        placeholder: "---",
                        allowClear: true,
                    });
                };

                $("#related_users").on('change', function(e) {
                    @this.related_users = $(this).val();
                });

                $.select2ModalCompany();

                Livewire.hook('message.processed', (message, component) => {
                    $.select2ModalCompany();
                });

            })(jQuery);

        });
    </script>

@endpush
