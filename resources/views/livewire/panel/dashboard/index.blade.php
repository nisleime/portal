<div>

    <div class="row">
        <livewire:panel.dashboard.card-info :user="$user" />
    </div>

    <div class="row">

        @if ($user->is_admin == 'S')

            <div class="col-100 mt-30">

                <div class="card">

                    <div class="heading split-col">
                        <div class="col">
                            <span class="title">Notas por período</span><br>
                            {!! App\Helpers\Format::periodHtml($docs_per_period_search) !!}
                        </div>
                        <div class="col">
                            <a href="#" class="btn btn-gray" title="Filtrar" data-trigger="modal" data-modal="#modal-filter-doc-per-period">
                                <i class="fas fa-filter"></i>
                            </a>
                        </div>
                    </div>

                    <div class="body">

                        <div class="row">

                            <div class="col-50">
                                <livewire:panel.dashboard.invoice-qty-total :user="$user" />
                            </div>

                            <div class="col-50">
                                <livewire:panel.dashboard.invoice-per-month :user="$user" />
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        @endif

    </div>

    <div class="row">

        <div class="col-100 mt-30 mb-30">

            <div class="card">

                <div class="heading split-col">

                    <div class="col">
                        <span class="title">Documentos</span><br>
                        {!! App\Helpers\Format::periodHtml($docs_search) !!}
                    </div>

                    <div class="col">
                        <a href="{{ $reportRoute }}" target="_blank" class="btn btn-gray" title="Relatório">
                            <i class="fas fa-file-alt"></i>
                        </a>
                        <a href="#" class="btn btn-gray ml-10" title="Download de todos documentos"
                            wire:click.prevent="$emit('eventDownloadCompressed', '{{ $doc_type }}')">
                            <i class="fas fa-download"></i>
                        </a>
                        <a href="#" class="btn btn-gray ml-10" title="Filtrar" data-trigger="modal"
                            data-modal="#modal-filter-doc">
                            <i class="fas fa-filter"></i>
                        </a>
                    </div>

                </div>

                <div class="body">

                    <div class="row">

                        <livewire:panel.dashboard.card-info-document :user="$user" />

                        <div class="col-100">

                            <div wire:ignore class="tab-main">

                                <ul class="nav">
                                    <li class="active" wire:click.prevent="$emitTo('panel.dashboard.index', 'eventDocType', 'invoice')">
                                        <a href="#invoices">Documento Fiscal</a>
                                    </li>
                                    <li>
                                        <a href="#events" wire:click.prevent="$emitTo('panel.dashboard.index', 'eventDocType', 'event')">Cancelamento / Carta de Correção</a>
                                    </li>
                                    <li>
                                        <a href="#disable" wire:click.prevent="$emitTo('panel.dashboard.index', 'eventDocType', 'disable')">Inutilização</a>
                                    </li>
                                </ul>

                                <div class="content pt-30 pb-0">

                                    <div id="invoices" class="body active">
                                        <livewire:panel.dashboard.invoice :user="$user" />
                                    </div>

                                    <div id="events" class="body">
                                        <livewire:panel.dashboard.event :user="$user" />
                                    </div>

                                    <div id="disable" class="body">
                                        <livewire:panel.dashboard.disable :user="$user" />
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@section('title', 'Dashboard')

@push('modals')
    <livewire:panel.dashboard.filter-doc :user="$user" />
    <livewire:panel.dashboard.filter-doc-per-period :user="$user" />
    <livewire:panel.dashboard.detail-doc :user="$user" />
@endpush

@push('plugins-styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/cute-alert/style.css') }}">
@endpush

@push('plugins-scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/i18n/pt-BR.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-mask/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs/chart.js') }}"></script>
    <script src="{{ asset('assets/plugins/cute-alert/cute-alert.js') }}"></script>
@endpush

@push('component-styles')

@endpush

@push('component-scripts')

    <script>
        document.addEventListener('livewire:load', function() {

            (function($) {

                $('.mask-date').mask('00/00/0000');

                Livewire.hook('message.processed', (message, component) => {
                    $('.mask-date').mask('00/00/0000');
                });

            })(jQuery);

        });
    </script>

@endpush
