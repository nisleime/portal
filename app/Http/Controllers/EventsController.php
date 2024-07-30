<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\EventDocument;
use NFePHP\DA\NFe\Daevento;
use Exception;

class EventsController extends Controller
{
    function xml_attribute($object, $attribute)
    {
        if (isset($object[$attribute]))
            return (string) $object[$attribute];
    }

    public function cancelamento_cce(Request $request)
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            if ($request->hasFile('file') && $request->file('file')->isValid()) {

                // Recupera a extensão do arquivo
                $extension = $request->file->extension();
                $nameFile = $request->file->getClientOriginalName();
                $size = $request->file->getSize();

                $xml = simplexml_load_file($request->file);

                $tipo_ambiente    = $xml->evento[0]->infEvento[0]->tpAmb;
                $cnpj             = $xml->evento[0]->infEvento[0]->CNPJ;
                $chave_nfe        = $xml->evento[0]->infEvento[0]->chNFe;
                $dh_evento        = $xml->evento[0]->infEvento[0]->dhEvento;
                $tipo_evento      = $xml->evento[0]->infEvento[0]->tpEvento;
                $numero_evento    = $xml->evento[0]->infEvento[0]->nSeqEvento;

                $data = str_replace('/', '-', $dh_evento);
                $mesano    = date('Ym', strtotime($data));
                $dh_evento  = date('Y-m-d', strtotime($dh_evento));

                if ($tipo_evento == 110110) {
                    $correcao         = $xml->evento[0]->infEvento[0]->detEvento->xCorrecao;
                    $desc_evento      = $xml->evento[0]->infEvento[0]->detEvento->descEvento;
                    $numero_protocolo = $xml->retEvento[0]->infEvento->nProt;
                    $status_evento    = $xml->retEvento[0]->infEvento->cStat;
                    $justificativa    = "";
                    $doc = 'CCe';
                } else {
                    $desc_evento      = $xml->evento[0]->infEvento[0]->detEvento->descEvento;
                    $numero_protocolo = $xml->retEvento[0]->infEvento->nProt;
                    $justificativa    = $xml->evento[0]->infEvento[0]->detEvento->xJust;
                    $status_evento    = $xml->retEvento[0]->infEvento->cStat;
                    $correcao         = "";
                    $doc = 'Cancelamento';
                }

                // Define modelo do documento
                $mod = substr($chave_nfe, 20, 2);

                // Define finalmente o nome
                $nameFile = "{$nameFile}";

                // Faz o upload para a pasta doc temporariamente
                $upload = $request->file->storeAs('docs', $nameFile);
                // pegar o caminho real onde foi feito o upload
                $url = Storage::url($upload);

                // Carrega o xml que acbou de ser salvo
                // $xml->load($url);

                // Novo endereço do upload
                $url_new = '/docs/eventos/' . $cnpj . '/' . $mod . '/' . $doc . '/' . $mesano . '/' . $nameFile;

                if (Storage::exists($url_new)) {
                    Storage::delete($url_new);
                }

                //Storage::delete($upload);
                Storage::move($upload, $url_new);

                $campos = [
                    'environment_type' => $tipo_ambiente,
                    'cnpj' => $cnpj,
                    'model' => $mod,
                    'nfe_key' => $chave_nfe,
                    'event_dh' => $dh_evento,
                    'event_type' => $tipo_evento,
                    'event_number' => $numero_evento,
                    'event_desc' => $desc_evento,
                    'protocol_number' => $numero_protocolo,
                    'justification' => mb_strtoupper($justificativa),
                    'event_status' => $status_evento,
                    'correction' => $correcao,
                    'size' => $size,
                    'path_xml' => $url_new
                ];

                // Apaga no banco de dados a linha do arquivo duplicado ou seja quando o arquivo e cancelado
                $docs =  DB::table('event_documents')->where([['nfe_key', '=', $chave_nfe], ['protocol_number', '=', $numero_protocolo]])->get();

                if (!empty($docs)) {
                    foreach ($docs as $doc) {
                        DB::table('event_documents')->where('id', '=', $doc->id)->delete();
                    }
                }

                DB::table('event_documents')->insert($campos);

                if (!$upload) {
                    return response()->json([
                        'msg' => 'Erro ao fazer upload.'
                    ], 200);
                } else {
                    return response()->json([
                        'msg' => '100'
                    ], 200);
                }
            }
        } else {
            return response()->json([
                'msg' => 'Você não tem permissão.'
            ], 200);
        }
    }

    public function cancelamento_cce_cte(Request $request)
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            if ($request->hasFile('file') && $request->file('file')->isValid()) {

                // Recupera a extensão do arquivo
                $extension = $request->file->extension();
                $nameFile = $request->file->getClientOriginalName();
                $size = $request->file->getSize();

                $xml = simplexml_load_file($request->file);

                $tipo_ambiente    = $xml->eventoCTe[0]->infEvento[0]->tpAmb;
                $cnpj             = $xml->eventoCTe[0]->infEvento[0]->CNPJ;
                $chave_cte        = $xml->eventoCTe[0]->infEvento[0]->chCTe;
                $dh_evento        = $xml->eventoCTe[0]->infEvento[0]->dhEvento;
                $tipo_evento      = $xml->eventoCTe[0]->infEvento[0]->tpEvento;
                $numero_evento    = $xml->eventoCTe[0]->infEvento[0]->nSeqEvento;

                $data = str_replace('/', '-', $dh_evento);
                $mesano    = date('Ym', strtotime($data));
                $dh_evento  = date('Y-m-d', strtotime($dh_evento));

                if ($tipo_evento == 110110) {
                    $correcao         = $xml->eventoCTe[0]->infEvento[0]->detEvento->xCorrecao;
                    $desc_evento      = $xml->eventoCTe[0]->infEvento[0]->detEvento->descEvento;
                    $numero_protocolo = $xml->retEventoCTe[0]->infEvento->nProt;
                    $status_evento    = $xml->retEventoCTe[0]->infEvento->cStat;
                    $justificativa    = "";
                    $doc = 'Carta de correção';
                } else {
                    $desc_evento      = $xml->eventoCTe[0]->infEvento[0]->detEvento->descEvento;
                    $numero_protocolo = $xml->retEventoCTe[0]->infEvento->nProt;
                    $justificativa    = $xml->eventoCTe[0]->infEvento[0]->detEvento->xJust;
                    $status_evento    = $xml->retEventoCTe[0]->infEvento->cStat;
                    $correcao         = "";
                    $doc = 'Cancelamento';
                }

                // Define modelo do documento
                $mod = substr($chave_cte, 20, 2);

                // Define finalmente o nome
                $nameFile = "{$nameFile}";

                // Faz o upload para a pasta doc temporariamente
                $upload = $request->file->storeAs('docs', $nameFile);
                // pegar o caminho real onde foi feito o upload
                $url = Storage::url($upload);

                // Carrega o xml que acbou de ser salvo
                // $xml->load($url);

                // Novo endereço do upload
                $url_new = '/docs/eventos/' . $cnpj . '/' . $mod . '/' . $doc . '/' . $mesano . '/' . $nameFile;

                if (Storage::exists($url_new)) {
                    Storage::delete($url_new);
                }

                //Storage::delete($upload);
                Storage::move($upload, $url_new);

                $campos = [
                    'environment_type' => $tipo_ambiente,
                    'cnpj' => $cnpj,
                    'model' => $mod,
                    'nfe_key' => $chave_cte,
                    'event_dh' => $dh_evento,
                    'event_type' => $tipo_evento,
                    'event_number' => $numero_evento,
                    'event_desc' => $doc,
                    'protocol_number' => $numero_protocolo,
                    'justification' => mb_strtoupper($justificativa),
                    'event_status' => $status_evento,
                    'correction' => $correcao,
                    'size' => $size,
                    'path_xml' => $url_new
                ];

                // Apaga no banco de dados a linha do arquivo duplicado ou seja quando o arquivo e cancelado
                $docs =  DB::table('event_documents')->where([['nfe_key', '=', $chave_cte], ['protocol_number', '=', $numero_protocolo]])->get();

                if (!empty($docs)) {
                    foreach ($docs as $doc) {
                        DB::table('event_documents')->where('id', '=', $doc->id)->delete();
                    }
                }

                DB::table('event_documents')->insert($campos);

                if (!$upload) {
                    return response()->json([
                        'msg' => 'Erro ao fazer upload.'
                    ], 200);
                } else {
                    return response()->json([
                        'msg' => '100'
                    ], 200);
                }
            }
        } else {
            return response()->json([
                'msg' => 'Você não tem permissão.'
            ], 200);
        }
    }

    public function inutilizacao_nfenfce(Request $request)
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            if ($request->hasFile('file') && $request->file('file')->isValid()) {

                // Recupera a extensão do arquivo
                $extension = $request->file->extension();
                $nameFile = $request->file->getClientOriginalName();
                $size = $request->file->getSize();

                $xml = simplexml_load_file($request->file);

                $tipo_ambiente      = $xml->inutNFe[0]->infInut[0]->tpAmb;
                $dh_evento          = $xml->retInutNFe[0]->infInut[0]->dhRecbto;
                $servico            = $xml->inutNFe[0]->infInut[0]->xServ;
                $uf                 = $xml->inutNFe[0]->infInut[0]->cUF;
                $ano                = $xml->inutNFe[0]->infInut[0]->ano;
                $cnpj               = $xml->inutNFe[0]->infInut[0]->CNPJ;
                $modelo             = $xml->inutNFe[0]->infInut[0]->mod;
                $serie              = $xml->inutNFe[0]->infInut[0]->serie;
                $numero_inicio      = $xml->inutNFe[0]->infInut[0]->nNFIni;
                $numero_fim         = $xml->inutNFe[0]->infInut[0]->nNFFin;
                $justificativa      = $xml->inutNFe[0]->infInut[0]->xJust;
                $status_evento      = $xml->retInutNFe[0]->infInut[0]->cStat;
                $numero_protocolo   = $xml->retInutNFe[0]->infInut[0]->nProt;

                $data = str_replace('/', '-', $dh_evento);
                $mesano    = date('Ym', strtotime($data));
                $dh_evento  = date('Y-m-d', strtotime($dh_evento));

                // Define finalmente o nome
                $nameFile = "{$nameFile}";

                // Faz o upload para a pasta doc temporariamente
                $upload = $request->file->storeAs('docs', $nameFile);
                // pegar o caminho real onde foi feito o upload
                $url = Storage::url($upload);

                // Carrega o xml que acbou de ser salvo
                //$xml->load($url);

                // Novo endereço do upload
                $url_new = '/docs/eventos/' . $cnpj . '/' . $modelo . '/Inutilizacao' . '/' . $mesano . '/' . $nameFile;

                if (Storage::exists($url_new)) {
                    Storage::delete($url_new);
                }

                //Storage::delete($upload);
                Storage::move($upload, $url_new);

                $campos = [
                    'environment_type' => $tipo_ambiente,
                    'event_dh' => $dh_evento,
                    'service' => $servico,
                    'uf' => $uf,
                    'year' => $ano,
                    'cnpj' => $cnpj,
                    'model' => $modelo,
                    'series' => $serie,
                    'number_start' => $numero_inicio,
                    'number_end' => $numero_fim,
                    'justification' => mb_strtoupper($justificativa),
                    'event_status' => $status_evento,
                    'protocol_number' => $numero_protocolo,
                    'size' => $size,
                    'path_xml' => $url_new
                ];

                // Apaga no banco de dados a linha do arquivo duplicado ou seja quando o arquivo e cancelado
                $docs =  DB::table('disable_documents')->where([['cnpj', '=', $cnpj], ['protocol_number', '=', $numero_protocolo]])->get();

                if (!empty($docs)) {
                    foreach ($docs as $doc) {
                        DB::table('disable_documents')->where('id', '=', $doc->id)->delete();
                    }
                }

                try {
                    DB::table('disable_documents')->insert($campos);
                } catch (\Exception $e) {
                    echo $e;
                }

                if (!$upload) {
                    return response()->json([
                        'msg' => 'Erro ao fazer upload.'
                    ], 200);
                } else {
                    return response()->json([
                        'msg' => '100'
                    ], 200);
                }
            }
        } else {
            return response()->json([
                'msg' => 'Você não tem permissão.'
            ], 200);
        }
    }
    public function inutilizacao_cte(Request $request)
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            if ($request->hasFile('file') && $request->file('file')->isValid()) {

                // Recupera a extensão do arquivo
                $extension = $request->file->extension();
                $nameFile = $request->file->getClientOriginalName();
                $size = $request->file->getSize();

                $xml = simplexml_load_file($request->file);

                $tipo_ambiente      = $xml->inutCTe[0]->infInut[0]->tpAmb;
                $dh_evento          = $xml->retInutCTe[0]->infInut[0]->dhRecbto;
                $servico            = $xml->inutCTe[0]->infInut[0]->xServ;
                $uf                 = $xml->inutCTe[0]->infInut[0]->cUF;
                $ano                = $xml->inutCTe[0]->infInut[0]->ano;
                $cnpj               = $xml->inutCTe[0]->infInut[0]->CNPJ;
                $modelo             = $xml->inutCTe[0]->infInut[0]->mod;
                $serie              = $xml->inutCTe[0]->infInut[0]->serie;
                $numero_inicio      = $xml->inutCTe[0]->infInut[0]->nCTIni;
                $numero_fim         = $xml->inutCTe[0]->infInut[0]->nCTFin;
                $justificativa      = $xml->inutCTe[0]->infInut[0]->xJust;
                $status_evento      = $xml->retInutCTe[0]->infInut[0]->cStat;
                $numero_protocolo   = $xml->retInutCTe[0]->infInut[0]->nProt;

                $data = str_replace('/', '-', $dh_evento);
                $mesano    = date('Ym', strtotime($data));
                $dh_evento  = date('Y-m-d', strtotime($dh_evento));

                // Define finalmente o nome
                $nameFile = "{$nameFile}";

                // Faz o upload para a pasta doc temporariamente
                $upload = $request->file->storeAs('docs', $nameFile);
                // pegar o caminho real onde foi feito o upload
                $url = Storage::url($upload);

                // Carrega o xml que acbou de ser salvo
                //$xml->load($url);

                // Novo endereço do upload
                $url_new = '/docs/eventos/' . $cnpj . '/' . $modelo . '/Inutilizacao' . '/' . $mesano . '/' . $nameFile;

                if (Storage::exists($url_new)) {
                    Storage::delete($url_new);
                }

                //Storage::delete($upload);
                Storage::move($upload, $url_new);

                $campos = [
                    'environment_type' => $tipo_ambiente,
                    'event_dh' => $dh_evento,
                    'service' => $servico,
                    'uf' => $uf,
                    'year' => $ano,
                    'cnpj' => $cnpj,
                    'model' => $modelo,
                    'series' => $serie,
                    'number_start' => $numero_inicio,
                    'number_end' => $numero_fim,
                    'justification' => mb_strtoupper($justificativa),
                    'event_status' => $status_evento,
                    'protocol_number' => $numero_protocolo,
                    'size' => $size,
                    'path_xml' => $url_new
                ];

                // Apaga no banco de dados a linha do arquivo duplicado ou seja quando o arquivo e cancelado
                $docs =  DB::table('disable_documents')->where([['cnpj', '=', $cnpj], ['protocol_number', '=', $numero_protocolo]])->get();

                if (!empty($docs)) {
                    foreach ($docs as $doc) {
                        DB::table('disable_documents')->where('id', '=', $doc->id)->delete();
                    }
                }

                try {
                    DB::table('disable_documents')->insert($campos);
                } catch (\Exception $e) {
                    echo $e;
                }

                if (!$upload) {
                    return response()->json([
                        'msg' => 'Erro ao fazer upload.'
                    ], 200);
                } else {
                    return response()->json([
                        'msg' => '100'
                    ], 200);
                }
            }
        } else {
            return response()->json([
                'msg' => 'Você não tem permissão.'
            ], 200);
        }
    }

    public function printEvent_nfenfce($id)
    {
        $document = EventDocument::with('company')->where('id', '=', $id)->first();

        $file = storage_path('app') . $document->path_xml;

        if (!File::exists($file)) {
            abort(404);
        }

        $xml = file_get_contents($file);

        $fontDefault = 'arial';
        $credits = 'Lion Sistemas';

        $dadosEmitente = [
            'razao' => $document->corporate_name,
            'logradouro' => $document->public_place,
            'numero' => $document->home_number,
            'complemento' => $document->complement,
            'bairro' => $document->district,
            'CEP' => $document->zip_code,
            'municipio' => $document->county,
            'UF' => $document->uf,
            'telefone' => $document->phone_number,
            'email' => $document->email
        ];

        try {

            $daevento = new Daevento($xml, $dadosEmitente);
            $daevento->debugMode(false);
            $daevento->setDefaultFont($fontDefault);
            $daevento->creditsIntegratorFooter($credits);
            $pdf = $daevento->render();
            header('Content-Type: application/pdf');
            ob_end_clean();

            echo $pdf;

        } catch (Exception $e) {
            return "Ocorreu um erro durante o processamento :" . $e->getMessage();
        }
    }
}
