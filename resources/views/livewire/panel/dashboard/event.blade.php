<div>

    @if ($events->total() == 0)
        <div class="row">
            <div class="col-100 mb-30">
                <div class="alert alert-default">
                    <p>Nenhum documento</p>
                </div>
            </div>
        </div>
    @else

        <!-- table -->
        <div class="table-wrap {{ $events->lastPage() == 1 ? 'mb-30' : '' }}">

            <table>

                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;"></th>
                        <th>
                            <input type="checkbox" wire:model="check_all_docs">
                        </th>
                        <th>CNPJ</th>
                        <th>N.º protocolo</th>
                        <th class="text-center">N.º evento</th>
                        <th class="text-center">Evento</th>
                        <th class="text-center">Modelo</th>
                        <th class="text-center">Data/Emissão</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($events as $event)

                        <tr wire:key="event-{{ $event->id }}">

                            <td class="text-center">
                                <div class="dropdown">
                                    <a href="#" class="text-dark-gray"><i class="fas fa-ellipsis-v"></i></a>
                                    <div class="dropdown-menu left text-left">
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="$emitTo('panel.dashboard.detail-doc', 'eventDetailDoc', {{ $event->id }}, 'event')">
                                            <i class="fas fa-eye"></i>
                                            Ver detalhes
                                        </a>
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="downloadDocById({{ $event->id }}, 'xml')">
                                            <i class="fas fa-download"></i>
                                            Download xml
                                        </a>
                                        @switch($event->model)
                                            @case(55)
                                            <a class="dropdown-item" target="_blank"
                                                href="{{ route('panel.docs.print.event.nfenfce', ['id' => $event->id]) }}">
                                                <i class="fas fa-print"></i>
                                            Imprimir pdf
                                             </a> 
                                                @break
                                            @case(65)
                                            <a class="dropdown-item" target="_blank"
                                                href="{{ route('panel.docs.print.event.nfenfce', ['id' => $event->id]) }}">
                                                <i class="fas fa-print"></i>
                                                Imprimir pdf
                                            </a> 
                                                @break
                                            @case(59)
                                            <a class="dropdown-item" target="_blank"
                                                href="{{ route('panel.docs.print.event.sat', ['id' => $event->id]) }}">
                                                <i class="fas fa-print"></i>
                                                Imprimir pdf
                                            </a> 
                                                @break    
                                            @case(57)
                                                <a class="dropdown-item" target="_blank"
                                                    href="{{ route('panel.docs.print.event.cte', ['id' => $event->id]) }}">
                                                    <i class="fas fa-print"></i>
                                                    Imprimir pdf
                                                </a> 
                                                @break
                                                
                                        @endswitch
                                    </div>
                                </div>
                            </td>

                            <td>
                                <input type="checkbox" wire:model="check_doc.{{ $event->id }}">
                            </td>

                            @if (strlen($event->cnpj) > 11)
                                <td>{{ App\Helpers\Mask::run($event->cnpj, '##.###.###/####-##') }}</td>
                            @else
                                <td>{{ App\Helpers\Mask::run($event->cnpj, '###.###.###-##') }}</td>
                            @endif

                            <td>{{ $event->protocol_number }}</td>
                            <td class="text-center">{{ $event->event_number }}</td>
                            <td class="text-center">{{ $event->event_desc }}</td>
                            <td class="text-center">
                                @if ($event->model == 55)
                                    <span class="badge badge-blue">NF-e</span>
                                @elseif ($event->model == 57)
                                    <span class="badge badge-blue">CT-e</span>
                                @elseif ($event->model == 58)
                                    <span class="badge badge-blue">MDF-e</span>
                                @elseif ($event->model == 59)
                                    <span class="badge badge-blue">CF-e Sat</span>
                                @elseif ($event->model == 65)
                                    <span class="badge badge-blue">NFC-e</span>
                                @endif
                            </td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($event->event_dh)) }}</td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>
        <!-- table-wrap -->

        {{ $events->links() }}

    @endif

</div>
