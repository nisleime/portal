<div>

    <!-- modal sidebar -->
    <div wire:ignore.self class="modal-main sidebar" id="modal-filter-doc">

        <div class="content">

            <a href="#" class="close">x</a>

            <div class="header">
                <p>Filtrar (documentos)</p>
            </div>

            <div wire:ignore.self class="body scrollbar">

                <div class="form-wrap row pt-30 pb-15">

                    <div class="col-100 mb-15">
                        <div class="box-heading">
                            <h3>Periodo de emissão</h3>
                        </div>
                    </div>

                    <div class="group mb-15 col-50">
                        <label>De</label>
                        <input type="text" class="mask-date" placeholder="__/__/____" wire:model.defer="first_date">
                        @error('first_date') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="group mb-15 col-50">
                        <label>Até</label>
                        <input type="text" class="mask-date" placeholder="__/__/____" wire:model.defer="last_date">
                        @error('last_date') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-100 mt-15 mb-15">
                        <div class="box-heading">
                            <h3>Dados gerais</h3>
                        </div>
                    </div>

                    <div class="group mb-15 col-100">
                        <label>Tipos de ambiente</label>
                        <select id="doc_environment_types" class="select-two-modal-filter-doc"
                            wire:model.defer="environment_types" multiple>
                            <option value="1">Produção</option>
                            <option value="2">Homologação</option>
                        </select>
                        @error('environment_types') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="group mb-15 col-100">
                        <label>Empresas</label>
                        <select id="doc_related_companies" class="select-two-modal-filter-doc"
                            wire:model.defer="related_companies" multiple>
                            @foreach ($companies as $company)
                                <option value="{{ $company->cnpj_cpf }}">
                                    @if ($company->fantasy_name)
                                        {{ Str::upper($company->fantasy_name) }}
                                    @else
                                        {{ Str::upper($company->corporate_name) }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="group mb-15 col-50">
                        <label>N.º documento</label>
                        <input type="text" wire:model.defer="doc_number">
                        @error('doc_number') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="group mb-15 col-50">
                        <label>N.º protocolo</label>
                        <input type="text" wire:model.defer="protocol_number">
                        @error('protocol_number') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="group mb-15 col-100">
                        <label>Tipos de documento</label>
                        <select id="doc_types" class="select-two-modal-filter-doc" wire:model.defer="doc_types"
                            multiple>
                            <option value="55">NF-e</option>
                            <option value="57">CT-e</option>
                            <option value="58">MDF-e</option>
                            <option value="59">Entrada</option>
                            <option value="65">NFC-e</option>
                        </select>
                    </div>

                    <div class="group mb-15 col-100">
                        <label>Status do documento</label>
                        <select id="doc_status" class="select-two-modal-filter-doc" wire:model.defer="doc_status"
                            multiple>
                            <option value="100">Autorizado</option>
                            <option value="101">Cancelado</option>
                            <option value="150">Autorizada fora do prazo</option>
                            <option value="151">Cancelada fora do prazo</option>
                            <option value="110">Uso denegado</option>
                            <option value="102">Inutilização de N.º homologado</option>
                            <option value="135">Evento registrado</option>
                        </select>
                    </div>

                </div>

            </div>

            <div class="footer">
                <div class="row">

                    <div class="col-50">
                        <a href="#" class="btn btn-dark-gray btn-block" wire:click.prevent="resetSearch">
                            <i class="fas fa-redo-alt"></i>
                            Resetar
                        </a>
                    </div>

                    <div class="col-50">
                        <a href="#" class="btn btn-blue btn-block" wire:click.prevent="submit">
                            <i class="fas fa-filter"></i>
                            Aplicar
                        </a>
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

                $.select2ModalFilterDoc = function() {
                    $('.select-two-modal-filter-doc').select2({
                        language: "pt-BR",
                        placeholder: "---",
                        allowClear: true,
                    });
                };

                $("#doc_related_companies").on('change', function(e) {
                    @this.related_companies = $(this).val();
                });

                $("#doc_types").on('change', function(e) {
                    @this.doc_types = $(this).val();
                });

                $("#doc_status").on('change', function(e) {
                    @this.doc_status = $(this).val();
                });

                $("#doc_environment_types").on('change', function(e) {
                    @this.environment_types = $(this).val();
                });

                $.select2ModalFilterDoc();

                Livewire.hook('message.processed', (message, component) => {
                    $.select2ModalFilterDoc();
                });

            })(jQuery);

        });
    </script>

@endpush
