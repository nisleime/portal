<div>

    <!-- modal form -->
    <div wire:ignore.self class="modal-main" id="modal-detail-doc">

        <div class="dialog">

            <div class="content">

                <a href="#" class="close">x</a>

                <div class="header">
                    <p>Detalhes do documento</p>
                </div>

                <div class="body pt-30 pb-30">

                    <div class="table-wrap details">

                        <table>

                            @if (isset($details['cnpj_cpf']))
                                <tr>
                                    <th>CNPJ/CPF</th>
                                    @if (strlen($details['cnpj_cpf']) > 11)
                                        <td>{{ App\Helpers\Mask::run($details['cnpj_cpf'], '##.###.###/####-##') }}
                                        </td>
                                    @else
                                        <td>{{ App\Helpers\Mask::run($details['cnpj_cpf'], '###.###.###-##') }}</td>
                                    @endif
                                </tr>
                            @endif

                            @if (isset($details['cnpj']))
                                <tr>
                                    <th>CNPJ</th>
                                    <td>{{ App\Helpers\Mask::run($details['cnpj'], '##.###.###/####-##') }}</td>
                                </tr>
                            @endif

                            @if (isset($details['ie']))
                                <tr>
                                    <th>IE</th>
                                    <td>{{ $details['ie'] }}</td>
                                </tr>
                            @endif

                            @if (isset($details['model']))
                                <tr>
                                    <th>Modelo</th>
                                    <td>
                                        @if ($details['model'] == 55)
                                            <span class="badge badge-blue">NF-e</span>
                                        @elseif ($details['model'] == 57)
                                            <span class="badge badge-blue">CT-e</span>
                                        @elseif ($details['model'] == 58)
                                            <span class="badge badge-blue">MDF-e</span>
                                        @elseif ($details['model'] == 59)
                                            <span class="badge badge-blue">Entrada</span>
                                        @elseif ($details['model'] == 65)
                                            <span class="badge badge-blue">NFC-e</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            @if (isset($details['series']))
                                <tr>
                                    <th>Série</th>
                                    <td>{{ $details['series'] }}</td>
                                </tr>
                            @endif

                            @if (isset($details['number']))
                                <tr>
                                    <th>Número</th>
                                    <td>{{ $details['number'] }}</td>
                                </tr>
                            @endif

                            @if (isset($details['protocol']))
                                <tr>
                                    <th>Número protocolo</th>
                                    <td>{{ $details['protocol'] }}</td>
                                </tr>
                            @endif

                            @if (isset($details['protocol_number']))
                                <tr>
                                    <th>Número protocolo</th>
                                    <td>{{ $details['protocol_number'] }}</td>
                                </tr>
                            @endif

                            @if (isset($details['event_number']))
                                <tr>
                                    <th>Número evento</th>
                                    <td>{{ $details['event_number'] }}</td>
                                </tr>
                            @endif

                            @if (isset($details['number_start']))
                                <tr>
                                    <th>Número começo</th>
                                    <td>{{ $details['number_start'] }}</td>
                                </tr>
                            @endif

                            @if (isset($details['number_end']))
                                <tr>
                                    <th>Número final</th>
                                    <td>{{ $details['number_end'] }}</td>
                                </tr>
                            @endif

                            @if (isset($details['key']))
                                <tr>
                                    <th>Chave</th>
                                    <td>{{ $details['key'] }}</td>
                                </tr>
                            @endif

                            @if (isset($details['nfe_key']))
                                <tr>
                                    <th>Chave</th>
                                    <td>{{ $details['nfe_key'] }}</td>
                                </tr>
                            @endif

                            @if (isset($details['month_year']))
                                <tr>
                                    <th>Mês/Ano</th>
                                    <td>{{ $details['month_year'] }}</td>
                                </tr>
                            @endif

                            @if (isset($details['issue_dh']))
                                <tr>
                                    <th>Data emissão</th>
                                    <td>{{ date('d/m/Y', strtotime($details['issue_dh'])) }}</td>
                                </tr>
                            @endif

                            @if (isset($details['event_dh']))
                                <tr>
                                    <th>Data emissão</th>
                                    <td>{{ date('d/m/Y', strtotime($details['event_dh'])) }}</td>
                                </tr>
                            @endif

                            @if (isset($details['environment_type']))
                                <tr>
                                    <th>Tipo ambiente</th>
                                    <td>
                                        @if ($details['environment_type'] == 1)
                                            Produção
                                        @elseif ($details['environment_type'] == 2)
                                            Homologação
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            @if (isset($details['event_type']))
                                <tr>
                                    <th>Tipo de evento</th>
                                    <td>{{ $details['event_type'] }}</td>
                                </tr>
                            @endif

                            @if (isset($details['status_xml']))
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if ($details['status_xml'] == 100)
                                            <span class="badge badge-green badge-round">Autorizada</span>
                                        @elseif ($details['status_xml'] == 101)
                                            <span class="badge badge-red badge-round">Cancelada</span>
                                        @elseif ($details['status_xml'] == 150)
                                            <span class="badge badge-green badge-round">Autorizada fora do prazo</span>
                                        @elseif ($details['status_xml'] == 151)
                                            <span class="badge badge-red badge-round">Cancelada fora do prazo</span>
                                        @elseif ($details['status_xml'] == 110)
                                            <span class="badge badge-default badge-round">Uso denegado</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            @if (isset($details['event_status']))
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if ($details['event_status'] == 102)
                                            <span class="badge badge-red badge-round">Inutilização de número
                                                homologado</span>
                                        @elseif ($details['event_status'] == 135)
                                            <span class="badge badge-red badge-round">Evento registrado</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            @if (isset($details['event_desc']))
                                <tr>
                                    <th>Descrição</th>
                                    <td>{{ empty($details['event_desc']) ? '...' : $details['event_desc'] }}</td>
                                </tr>
                            @endif

                            @if (isset($details['justification']))
                                <tr>
                                    <th>Justificativa</th>
                                    <td>{{ empty($details['justification']) ? '...' : $details['justification'] }}
                                    </td>
                                </tr>
                            @endif

                            @if (isset($details['correction']))
                                <tr>
                                    <th>Correção</th>
                                    <td>{{ empty($details['correction']) ? '...' : $details['correction'] }}</td>
                                </tr>
                            @endif

                            @if (isset($details['vNF']))
                                <tr>
                                    <th>Valor</th>
                                    <td>R$ {{ number_format($details['vNF'], 2, ',', '.') }}</td>
                                </tr>
                            @endif

                        </table>

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

                Livewire.hook('message.processed', (message, component) => {

                });

            })(jQuery);

        });
    </script>

@endpush
