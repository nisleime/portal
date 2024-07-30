@extends('report.app')

@section('title', 'Relatório de notas')

@section('content')

    <h2>Relatório de notas fiscais</h2>
    <small>Período: {!! App\Helpers\Format::periodHtml($searchArgsToDocReport) !!}</small>

    <hr>

    <h4>Visão geral</h4>

    <table>
        <tr>
            <th>Modelo</th>
            <th>Status</th>
            <th>Quantidade</th>
            <th>Total</th>
        </tr>

        @foreach ($invoicesOverview as $value)
            <tr>
                <td>{{ $value['model'] }}</td>
                <td>{{ $value['status_xml'] }}</td>
                <td>{{ $value['qty'] }}</td>
                <td>R$ {{ number_format($value['total'], 2, ',', '.') }}</td>
            </tr>
        @endforeach

    </table>

    <h4>Lista de notas</h4>

    <table>
        <tr>
            <th>CNPJ/CPF</th>
            <th>Série</th>
            <th>N.º</th>
            <th>Chave</th>
            <th>Valor</th>
            <th>Modelo</th>
            <th>Status</th>
            <th>Data Emissão</th>
        </tr>

        @foreach ($invoices as $invoice)
            <tr>
                @if (strlen($invoice->cnpj_cpf) > 11)
                    <td>{{ App\Helpers\Mask::run($invoice->cnpj_cpf, '##.###.###/####-##') }}</td>
                @else
                    <td>{{ App\Helpers\Mask::run($invoice->cnpj_cpf, '###.###.###-##') }}</td>
                @endif

                <td>{{ $invoice->series }}</td>
                <td>{{ $invoice->number }}</td>
                <td>{{ $invoice->key }}</td>
                <td>R$ {{ number_format($invoice->vNF, 2, ',', '.') }}</td>
                <td class="text-center">
                    @if ($invoice->model == 55)
                        <span class="badge badge-blue">NF-e</span>
                    @elseif ($invoice->model == 57)
                        <span class="badge badge-blue">CT-e</span>
                    @elseif ($invoice->model == 58)
                        <span class="badge badge-blue">MDF-e</span>
                    @elseif ($invoice->model == 59)
                        <span class="badge badge-blue">Entrada</span>
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
                <td class="text-center">{{ date('d/m/Y', strtotime($invoice->issue_dh)) }}</td>
            </tr>

        @endforeach

    </table>

@endsection
