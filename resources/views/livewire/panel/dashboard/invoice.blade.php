<div>

    @if ($invoices->total() == 0)
        <div class="row">
            <div class="col-100 mb-30">
                <div class="alert alert-default">
                    <p>Nenhum documento</p>
                </div>
            </div>
        </div>
    @else

        <!-- table -->
        <div class="table-wrap {{ $invoices->lastPage() == 1 ? 'mb-30' : '' }}">

            <table>

                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;"></th>
                        <th>
                            <input type="checkbox" wire:model="check_all_docs">
                        </th>
                        <th>CNPJ/CPF</th>
                        <th>Série</th>
                        <th>N.º</th>
                        <th>Valor</th>
                        <th class="text-center">Modelo</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Data Emissão</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($invoices as $invoice)

                        <tr wire:key="invoice-{{ $invoice->id }}">
                            <td class="text-center">
                                <div class="dropdown">
                                    <a href="#" class="text-dark-gray"><i class="fas fa-ellipsis-v"></i></a>
                                    <div class="dropdown-menu left text-left">
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="$emitTo('panel.dashboard.detail-doc', 'eventDetailDoc', {{ $invoice->id }}, 'invoice')">
                                            <i class="fas fa-eye"></i>
                                            Ver detalhes
                                        </a>
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="downloadDocById({{ $invoice->id }}, 'xml')">
                                            <i class="fas fa-download"></i>
                                            Download xml
                                        </a>
                                        
                                            <a class="dropdown-item" target="_blank"
                                                href="{{ route('panel.docs.print.invoice', ['id' => $invoice->id]) }}">
                                                <i class="fas fa-print"></i>
                                                Imprimir pdf
                                            </a>
                                                                          </div>
                                </div>
                            </td>

                            <td>
                                <input type="checkbox" wire:model="check_doc.{{ $invoice->id }}">
                            </td>

                            @if (strlen($invoice->cnpj_cpf) > 11)
                                <td>{{ App\Helpers\Mask::run($invoice->cnpj_cpf, '##.###.###/####-##') }}</td>
                            @else
                                <td>{{ App\Helpers\Mask::run($invoice->cnpj_cpf, '###.###.###-##') }}</td>
                            @endif

                            <td>{{ $invoice->series }}</td>
                            <td>{{ $invoice->number }}</td>
                            <td>R$ {{ number_format($invoice->vNF, 2, ',', '.') }}</td>
                            <td class="text-center">
                                @if ($invoice->model == 55)
                                    <span class="badge badge-blue">NF-e</span>
                                @elseif ($invoice->model == 57)
                                    <span class="badge badge-blue">CT-e</span>
                                @elseif ($invoice->model == 58)
                                    <span class="badge badge-blue">MDF-e</span>
                                @elseif ($invoice->model == 59)
                                    <span class="badge badge-blue">CF-e Sat</span>
                                @elseif ($invoice->model == 65)
                                    <span class="badge badge-blue">NFC-e</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($invoice->status_xml == 100)
                                    <span class="badge badge-green badge-round">Autorizada</span>
                                @elseif ($invoice->status_xml == 101)
                                    <span class="badge badge-red badge-round">Cancelada</span>
                                @elseif ($invoice->status_xml == 150)
                                    <span class="badge badge-green badge-round">Autorizada fora do prazo</span>
                                @elseif ($invoice->status_xml == 151)
                                    <span class="badge badge-red badge-round">Cancelada fora do prazo</span>
                                @elseif ($invoice->status_xml == 110)
                                    <span class="badge badge-default badge-round">Uso denegado</span>
                                @endif
                            </td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($invoice->issue_dh)) }}
                            </td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>
        <!-- table-wrap -->

        {{ $invoices->links() }}

    @endif

</div>
