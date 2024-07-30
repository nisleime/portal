<div>

    <!-- page-header -->
    <div class="page-header split-col">

        <div class="col">
            <h3 class="title">Empresas</h3>
        </div>

        <div class="col">
            <a href="#" class="btn btn-default" data-trigger="modal" data-modal="#modal-data-to-company"
                wire:click.prevent="$emitTo('panel.company.modal-data-to-company', 'eventAction', 'store')">
                <i class="fas fa-plus"></i>
            </a>
        </div>

    </div>

    @if ($companies->total() == 0)
        <div class="row">
            <div class="col-100 mt-30">
                <div class="alert alert-default">
                    <p>
                        Nenhuma empresa,
                        <a href="#" class="text-blue" data-trigger="modal" data-modal="#modal-data-to-company"
                            wire:click.prevent="$emitTo('panel.company.modal-data-to-company', 'eventAction', 'store')">cadastrar!</a>
                    </p>
                </div>
            </div>
        </div>
    @else

        <!-- table -->
        <div class="table-wrap mt-30">

            <table>

                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;"></th>
                        <th>Nome Fantasia</th>
                        <th>CNPJ/CPF</th>
                        <th class="text-center">Usuários vinculados</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($companies as $company)

                        <tr>
                            <td class="text-center">
                                <div class="dropdown">
                                    <a href="#" class="text-dark-gray"><i class="fas fa-ellipsis-v"></i></a>
                                    <div class="dropdown-menu left text-left">
                                        <a class="dropdown-item" href="#" data-trigger="modal"
                                            data-modal="#modal-data-to-company"
                                            wire:click.prevent="$emitTo('panel.company.modal-data-to-company', 'eventAction', 'edit', {{ $company['id'] }})">
                                            <i class="far fa-edit"></i>
                                            Editar
                                        </a>
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="$emit('eventCuteConfirmDeleteCompany', {{ $company['id'] }})">
                                            <i class="far fa-minus-square"></i>
                                            Excluir
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $company['fantasy_name'] }}</td>

                            @if (strlen($company['cnpj_cpf']) > 11)
                                <td>{{ App\Helpers\Mask::run($company['cnpj_cpf'], '##.###.###/####-##') }}</td>
                            @else
                                <td>{{ App\Helpers\Mask::run($company['cnpj_cpf'], '###.###.###-##') }}</td>
                            @endif

                            <td class="text-center">{{ $company['users_count'] }}</td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>
        <!-- table-wrap -->

        {{ $companies->links() }}

    @endif

</div>

@section('title', 'Empresas')

@push('modals')
    <livewire:panel.company.modal-data-to-company />
@endpush

@push('plugins-styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/cute-alert/style.css') }}">
@endpush

@push('plugins-scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/i18n/pt-BR.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-mask/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/cute-alert/cute-alert.js') }}"></script>
@endpush


@push('component-styles')

@endpush

@push('component-scripts')

    <script>
        document.addEventListener('livewire:load', function() {

            (function($) {

                $('.mask-cnpj_cpf').length > 11 ? $('.mask-cnpj_cpf').mask('00.000.000/0000-00', MASK_CNPJ_CPF) : $('.mask-cnpj_cpf').mask('000.000.000-00#', MASK_CNPJ_CPF);
                $('.mask-phone').mask(SPMaskBehavior, spOptions);

                Livewire.on('eventCuteConfirmDeleteCompany', (id) => {

                    cuteAlert({
                        'type': 'question',
                        'title': "Confirmação!",
                        'message': "Quer excluir permanentemente?",
                        'confirmText': "Sim",
                        'cancelText': "Não",
                    }).then((e) => {

                        if (e == "confirm") {
                            @this.deleteCompany(id)
                        }

                    });

                });

                Livewire.hook('message.processed', (message, component) => {
                    //
                });

            })(jQuery);

        });
    </script>

@endpush
