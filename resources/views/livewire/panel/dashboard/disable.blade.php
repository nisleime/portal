<div>

    @if ($disables->total() == 0)
        <div class="row">
            <div class="col-100 mb-30">
                <div class="alert alert-default">
                    <p>Nenhum documento</p>
                </div>
            </div>
        </div>
    @else

        <!-- table -->
        <div class="table-wrap {{ $disables->lastPage() == 1 ? 'mb-30' : '' }}">

            <table>

                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;"></th>
                        <th>
                            <input type="checkbox" wire:model="check_all_docs">
                        </th>

                        <th>CNPJ</th>
                        <th>Ano</th>
                        <th class="text-center">Modelo</th>
                        <th>Série</th>
                        <th>N.º Inicial</th>
                        <th>N.º Final</th>
                        <th>N.º Protocolo</th>
                        <th class="text-center">Data/Emissão</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($disables as $disable)

                        <tr wire:key="disable-{{ $disable->id }}">

                            <td class="text-center">
                                <div class="dropdown">
                                    <a href="#" class="text-dark-gray"><i class="fas fa-ellipsis-v"></i></a>
                                    <div class="dropdown-menu left text-left">
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="$emitTo('panel.dashboard.detail-doc', 'eventDetailDoc', {{ $disable->id }}, 'disable')">
                                            <i class="fas fa-eye"></i>
                                            Ver detalhes
                                        </a>
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="downloadDocById({{ $disable->id }}, 'xml')">
                                            <i class="fas fa-download"></i>
                                            Download xml
                                        </a>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <input type="checkbox" wire:model="check_doc.{{ $disable->id }}">
                            </td>

                            @if (strlen($disable->cnpj) > 11)
                                <td>{{ App\Helpers\Mask::run($disable->cnpj, '##.###.###/####-##') }}</td>
                            @else
                                <td>{{ App\Helpers\Mask::run($disable->cnpj, '###.###.###-##') }}</td>
                            @endif

                            <td>{{ $disable->year }}</td>

                            <td class="text-center">
                                @if ($disable->model == 55)
                                    <span class="badge badge-blue">NF-e</span>
                                @elseif ($disable->model == 57)
                                    <span class="badge badge-blue">CT-e</span>
                                @elseif ($disable->model == 58)
                                    <span class="badge badge-blue">MDF-e</span>
                                @elseif ($disable->model == 59)
                                    <span class="badge badge-blue">CF-e Sat</span>
                                @elseif ($disable->model == 65)
                                    <span class="badge badge-blue">NFC-e</span>
                                @endif
                            </td>

                            <td>{{ $disable->series }}</td>
                            <td>{{ $disable->number_start }}</td>
                            <td>{{ $disable->number_end }}</td>
                            <td>{{ $disable->protocol_number }}</td>

                            <td class="text-center">{{ date('d/m/Y', strtotime($disable['event_dh'])) }}</td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>
        <!-- table-wrap -->

        {{ $disables->links() }}

    @endif

</div>
