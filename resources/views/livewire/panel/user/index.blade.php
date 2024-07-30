<div>

    <!-- page-header -->
    <div class="page-header split-col">

        <div class="col">
            <h3 class="title">Usuários</h3>
        </div>

        <div class="col">
            <a href="#" class="btn btn-default" data-trigger="modal" data-modal="#modal-data-to-user"
                wire:click.prevent="$emitTo('panel.user.modal-data-to-user', 'eventAction', 'store')">
                <i class="fas fa-plus"></i>
            </a>
        </div>

    </div>

    @if ($users->total() == 0)
        <div class="row">
            <div class="col-100 mt-30">
                <div class="alert alert-default">
                    <p>
                        Nenhum usuário,
                        <a href="#" class="text-blue" data-trigger="modal" data-modal="#modal-data-to-user"
                            wire:click.prevent="$emitTo('panel.user.modal-data-to-user', 'eventAction', 'store')">cadastrar!</a>
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
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th class="text-center">Administrador</th>
                        <th class="text-center">Empresas vinculadas</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($users as $user)

                        <tr wire:key="user_{{ $user['id'] }}">
                            <td class="text-center">
                                <div class="dropdown">
                                    <a href="#" class="text-dark-gray"><i class="fas fa-ellipsis-v"></i></a>
                                    <div class="dropdown-menu left text-left">
                                        <a class="dropdown-item" href="#" data-trigger="modal"
                                            data-modal="#modal-data-to-user"
                                            wire:click.prevent="$emitTo('panel.user.modal-data-to-user', 'eventAction', 'edit', {{ $user['id'] }})">
                                            <i class="far fa-edit"></i>
                                            Editar
                                        </a>

                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="$emit('eventCuteConfirmDeleteUser', {{ $user['id'] }})">
                                            <i class="far fa-minus-square"></i>
                                            Excluir
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user['name'] }}</td>
                            <td>{{ $user['email'] }}</td>
                            <td class="text-center">{{ $user['is_admin'] == 'S' ? 'Sim' : 'Não' }}</td>
                            <td class="text-center">{{ $user['companies_count'] }}</td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>
        <!-- table-wrap -->

        {{ $users->links() }}

    @endif

</div>

@section('title', 'Usuários')

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

                Livewire.on('eventCuteConfirmDeleteUser', (id) => {

                    cuteAlert({
                        'type': 'question',
                        'title': "Confirmação!",
                        'message': "Quer excluir permanentemente?",
                        'confirmText': "Sim",
                        'cancelText': "Não",
                    }).then((e) => {

                        if (e == "confirm") {
                            @this.deleteUser(id)
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
