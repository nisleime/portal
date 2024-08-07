<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use NFePHP\DA\NFe\Danfe;
use NFePHP\DA\NFe\Danfce;
use NFePHP\DA\CTe\Dacte;
use NFePHP\DA\MDFe\Damdfe;
use App\Models\EventDocument;
use NFePHP\DA\CTe\Daevento;
use Exception;

class DocsController extends Controller
{
    function xml_attribute($object, $attribute)
    {
        if (isset($object[$attribute]))
            return (string) $object[$attribute];
    }

    public function nfe_nfce(Request $request)
    {
        if (isset($request->key) && $request->key == 'Sistema') {
            if ($request->hasFile('file') && $request->file('file')->isValid()) {

                // Recupera a extensão do arquivo
                $extension = $request->file->extension();
                $nameFile = $request->file->getClientOriginalName();
                $size = $request->file->getSize();

                $xml = simplexml_load_file($request->file);

                $nNF       = $xml->NFe->infNFe->ide->nNF;
                $serie     = $xml->NFe->infNFe->ide->serie;
                $mod       = $xml->NFe->infNFe->ide->mod;
                $chNFe     = $xml->protNFe->infProt->chNFe;
                $nProt     = $xml->protNFe->infProt->nProt;
                $IE        = $xml->NFe->infNFe->emit->IE;
                $dhEmi     = $xml->NFe->infNFe->ide->dhEmi;
                $tpAmb     = $xml->NFe->infNFe->ide->tpAmb;
                $cStat     = $xml->protNFe->infProt->cStat;

                switch ($cStat) {
                    case 100:
                        $cStat = 100;
                        break;
                    case 101:
                        $cStat = 101;
                        break;
                    case 135:
                        $cStat = 101;
                        break;
                    case 155:
                        $cStat = 101;
                        break;
                    case 150:
                        $cStat = 150;
                        break;
                    case 151:
                        $cStat = 151;
                        break;
                    case 110:
                        $cStat = 110;
                        break;
                    case 301:
                        $cStat = 110;
                        break;
                    case 302:
                        $cStat = 110;
                        break;
                    case 303:
                        $cStat = 110;
                        break;
                }

                $vNF = $xml->NFe->infNFe->total->ICMSTot->vNF;

                if ($serie >= 920) {
                    $CNPJCPF = $xml->NFe->infNFe->emit->CPF;
                } else {
                    $CNPJCPF = $xml->NFe->infNFe->emit->CNPJ;
                }

                $razao_social = $xml->NFe->infNFe->emit->xNome;
                $nome_fantasia = $xml->NFe->infNFe->emit->xFant;
                $logradouro   = $xml->NFe->infNFe->emit->enderEmit->xLgr;
                $numero   = $xml->NFe->infNFe->emit->enderEmit->nro;

                $bairro   = $xml->NFe->infNFe->emit->enderEmit->xBairro;
                $cep   = $xml->NFe->infNFe->emit->enderEmit->CEP;
                $municipio   = $xml->NFe->infNFe->emit->enderEmit->xMun;
                $uf   = $xml->NFe->infNFe->emit->enderEmit->UF;
                $telefone   = $xml->NFe->infNFe->emit->enderEmit->fone;
                //$email   = $xml->NFe->infNFe->emit->email;


                $data = str_replace('/', '-', $dhEmi);
                $mesano    = date('Ym', strtotime($data));
                $data_emissao  = date('Y-m-d', strtotime($dhEmi));

                // Define finalmente o nome
                $nameFile = "{$nameFile}";

                // Faz o upload para a pasta doc temporariamente
                $upload = $request->file->storeAs('docs', $nameFile);
                // pegar o caminho real onde foi feito o upload
                $url = Storage::url($upload);
                // Carrega o xml que acbou de ser salvo
                //$xml->load($url);

                // Novo endereço do upload
                $url_new = '/docs/' . $CNPJCPF . '/' . $IE . '/' . $mod . '/' . $mesano . '/' . $nameFile;

                if (Storage::exists($url_new)) {
                    Storage::delete($url_new);
                }

                // Storage::delete($upload);
                Storage::move($upload, $url_new);

                // aqui é a parte duplicada
                $empresas =  DB::table('companies')->Where('cnpj_cpf', '=', $CNPJCPF)->first();

                if (!$empresas) {

                    $campos = [
                        'cnpj_cpf' => $CNPJCPF,
                        'corporate_name' => mb_strtoupper($razao_social),
                        'fantasy_name' => mb_strtoupper($nome_fantasia),
                        //'email' => $email,
                        'public_place' => $logradouro,
                        'home_number' => $numero,
                        //'complement' => $complemento,
                        'district' => $bairro,
                        'zip_code' => $cep,
                        'county' => $municipio,
                        'uf' => $uf,
                        'phone_number' => $telefone
                    ];

                    DB::table('companies')->insert($campos);
                }

                //Apaga no banco de dados a linha do arquivo duplicado ou seja quando o arquivo e cancelado
                $docs =  DB::table('documents')->where('key', '=', $chNFe)->get();

                if (!empty($docs)) {
                    foreach ($docs as $doc) {
                        DB::table('documents')->where('id', '=', $doc->id)->delete();
                    }
                }

                $campos = [
                    'cnpj_cpf' => $CNPJCPF,
                    'ie' => $IE,
                    'model' => $mod,
                    'series' => $serie,
                    'number' => $nNF,
                    'key' => $chNFe,
                    'month_year' => $mesano,
                    'issue_dh' => $data_emissao,
                    'path_xml' => $url_new,
                    'protocol' => $nProt,
                    'environment_type' => $tpAmb,
                    'status_xml' => $cStat,
                    'vNF' => $vNF,
                    'size' => $size,
                ];

                DB::table('documents')->insert($campos);

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
	
//NF Entrada
    public function nfe(Request $request)
    {
        if (isset($request->key) && $request->key == 'Sistema') {
            if ($request->hasFile('file') && $request->file('file')->isValid()) {

                // Recupera a extensão do arquivo
                $extension = $request->file->extension();
                $nameFile = $request->file->getClientOriginalName();
                $size = $request->file->getSize();

                $xml = simplexml_load_file($request->file);

                $nNF       = $xml->NFe->infNFe->ide->nNF;
                $serie     = $xml->NFe->infNFe->ide->serie;
                $mod       = $xml->NFe->infNFe->ide->mod;
                $chNFe     = $xml->protNFe->infProt->chNFe;
                $nProt     = $xml->protNFe->infProt->nProt;
                $IE        = $xml->NFe->infNFe->emit->IE;
                $dhEmi     = $xml->NFe->infNFe->ide->dhEmi;
                $tpAmb     = $xml->NFe->infNFe->ide->tpAmb;
                $cStat     = $xml->protNFe->infProt->cStat;

                switch ($cStat) {
                    case 100:
                        $cStat = 100;
                        break;
                    case 101:
                        $cStat = 101;
                        break;
                    case 135:
                        $cStat = 101;
                        break;
                    case 155:
                        $cStat = 101;
                        break;
                    case 150:
                        $cStat = 150;
                        break;
                    case 151:
                        $cStat = 151;
                        break;
                    case 110:
                        $cStat = 110;
                        break;
                    case 301:
                        $cStat = 110;
                        break;
                    case 302:
                        $cStat = 110;
                        break;
                    case 303:
                        $cStat = 110;
                        break;
                }

                $vNF = $xml->NFe->infNFe->total->ICMSTot->vNF;

                if ($serie >= 920) {
                    $CNPJCPF = $xml->NFe->infNFe->dest->CPF;
                } else {
                    $CNPJCPF = $xml->NFe->infNFe->dest->CNPJ;
                }

                $razao_social = $xml->NFe->infNFe->dest->xNome;
                $nome_fantasia = $xml->NFe->infNFe->dest->xNome;
                $logradouro   = $xml->NFe->infNFe->dest->enderDest->xLgr;
                $numero   = $xml->NFe->infNFe->dest->enderDest->nro;

                $bairro   = $xml->NFe->infNFe->dest->enderDest->xBairro;
                $cep   = $xml->NFe->infNFe->dest->enderDest->CEP;
                $municipio   = $xml->NFe->infNFe->dest->enderDest->xMun;
                $uf   = $xml->NFe->infNFe->dest->enderDest->UF;
                $telefone   = $xml->NFe->infNFe->dest->enderDest->fone;
                //$email   = $xml->NFe->infNFe->emit->email;
				$cnpj_emit = $xml->NFe->infNFe->emit->CNPJ;


                $data = str_replace('/', '-', $dhEmi);
                $mesano    = date('Ym', strtotime($data));
                $data_emissao  = date('Y-m-d', strtotime($dhEmi));

                // Define finalmente o nome
                $nameFile = "{$nameFile}";

                // Faz o upload para a pasta doc temporariamente
                $upload = $request->file->storeAs('docs', $nameFile);
                // pegar o caminho real onde foi feito o upload
                $url = Storage::url($upload);
                // Carrega o xml que acbou de ser salvo
                //$xml->load($url);

                // Novo endereço do upload
                //$url_new = '/docs/entrada/' . $CNPJCPF . '/' . $IE . '/' . $mod . '/' . $mesano . '/' . $nameFile;
				$url_new = '/docs/entrada/' . $CNPJCPF . '/'. $nameFile;

                if (Storage::exists($url_new)) {
                    Storage::delete($url_new);
                }

                // Storage::delete($upload);
                Storage::move($upload, $url_new);

                // aqui é a parte duplicada
                $empresas =  DB::table('companies')->Where('cnpj_cpf', '=', $CNPJCPF)->first();

                if (!$empresas) {

                    $campos = [
                        'cnpj_cpf' => $CNPJCPF,
                        'corporate_name' => mb_strtoupper($razao_social),
                        'fantasy_name' => mb_strtoupper($nome_fantasia),
                        //'email' => $email,
                        'public_place' => $logradouro,
                        'home_number' => $numero,
                        //'complement' => $complemento,
                        'district' => $bairro,
                        'zip_code' => $cep,
                        'county' => $municipio,
                        'uf' => $uf,
                        'phone_number' => $telefone
                    ];

                    DB::table('companies')->insert($campos);
                }

                //Apaga no banco de dados a linha do arquivo duplicado ou seja quando o arquivo e cancelado
                $docs =  DB::table('documents')->where('key', '=', $chNFe)->get();

                if (!empty($docs)) {
                    foreach ($docs as $doc) {
                        DB::table('documents')->where('id', '=', $doc->id)->delete();
                    }
                }

                $campos = [
                    'cnpj_cpf' => $CNPJCPF,
					'cnpj_emit' => $cnpj_emit,
                    'ie' => $IE,
                    'model' => '59',//=> $mod,
                    'series' => $serie,
                    'number' => $nNF,
                    'key' => $chNFe,
                    'month_year' => $mesano,
                    'issue_dh' => $data_emissao,
                    'path_xml' => $url_new,
                    'protocol' => $nProt,
                    'environment_type' => $tpAmb,
                    'status_xml' => $cStat,
                    'vNF' => $vNF,
                    'size' => $size,
					'entrada' => 'S',
                ];

                DB::table('documents')->insert($campos);

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
//NF Entrada	

    public function sat(Request $request)
    {
        if (isset($request->key) && $request->key == 'Sistema') {
            if ($request->hasFile('file') && $request->file('file')->isValid()) {

                // Recupera a extensão do arquivo
                $extension = $request->file->extension();
                $nameFile = $request->file->getClientOriginalName();
                $size = $request->file->getSize();

                $xml = simplexml_load_file($request->file);

                $nNF       = $xml->infCFe->ide->nCFe;
                $serie     = $xml->infCFe->ide->nserieSAT;
                $mod       = $xml->infCFe->ide->mod;
                $chNFe     = substr($this->xml_attribute($xml->infCFe, 'Id'), 3, 46);

                $IE        = $xml->infCFe->emit->IE;
                $dhEmi     = date('Y-m-d', strtotime($xml->infCFe->ide->dEmi));
                $tpAmb     = $xml->infCFe->ide->tpAmb;

                if (isset($request->sat_status)) {
                    $cStat     = $request->sat_status;
                } else {
                    $cStat = '100';
                }
                $vNF       = $xml->infCFe->total->vCFe;

                //emitente
                $CNPJCPF = $xml->infCFe->emit->CNPJ;
                $razao_social =  $xml->infCFe->emit->xNome;
                $nome_fantasia = $xml->infCFe->emit->xNome;

                
                $data = str_replace('/', '-', $dhEmi);
                $mesano    = date('Ym', strtotime($data));
                $data_emissao  = date('Y-m-d', strtotime($dhEmi));

                // Define finalmente o nome
                $nameFile = "{$nameFile}";

                // Faz o upload para a pasta doc temporariamente
                $upload = $request->file->storeAs('docs', $nameFile);
                // pegar o caminho real onde foi feito o upload
                $url = Storage::url($upload);
                // Carrega o xml que acbou de ser salvo
                //$xml->load($url);

                // Novo endereço do upload
                $url_new = '/docs/' . $CNPJCPF . '/' . $IE . '/' . $mod . '/' . $mesano . '/' . $nameFile;

                if (Storage::exists($url_new)) {
                    Storage::delete($url_new);
                }

                //Storage::delete($upload);
                Storage::move($upload, $url_new);

                $empresas =  DB::table('companies')->Where('cnpj_cpf', '=', $CNPJCPF)->first();

                if (!$empresas) {

                    $campos = [
                        'cnpj_cpf' => $CNPJCPF,
                        'corporate_name' => mb_strtoupper($razao_social),
                        'fantasy_name' => mb_strtoupper($nome_fantasia)
                    ];

                    DB::table('companies')->insert($campos);
                }

                //Apaga no banco de dados a linha do arquivo duplicado ou seja quando o arquivo e cancelado
                $docs =  DB::table('documents')->where('key', '=', $chNFe)->get();

                if (!empty($docs)) {
                    foreach ($docs as $doc) {
                        DB::table('documents')->where('id', '=', $doc->id)->delete();
                    }
                }

                $campos = [
                    'cnpj_cpf' => $CNPJCPF,
                    'ie' => $IE,
                    'model' => $mod,
                    'series' => $serie,
                    'number' => $nNF,
                    'key' => $chNFe,
                    'month_year' => $mesano,
                    'issue_dh' => $data_emissao,
                    'path_xml' => $url_new,
                    'protocol' => 'SEM PROTOCOLO',
                    'environment_type' => $tpAmb,
                    'status_xml' => $cStat,
                    'vNF' => $vNF,
                    'size' => $size,
                ];


                DB::table('documents')->insert($campos);

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


    public function cte(Request $request)
    {
        if (isset($request->key) && $request->key == 'Sistema') {
            if ($request->hasFile('file') && $request->file('file')->isValid()) {

                // Recupera a extensão do arquivo
                $extension = $request->file->extension();
                $nameFile = $request->file->getClientOriginalName();
                $size = $request->file->getSize();

                $xml = simplexml_load_file($request->file);

                $nNF       = $xml->CTe->infCte->ide->nCT;
                $serie     = $xml->CTe->infCte->ide->serie;
                $mod       = $xml->CTe->infCte->ide->mod;
                $chNFe     = $xml->protCTe->infProt->chCTe;
                $nProt     = $xml->protCTe->infProt->nProt;
                $IE        = $xml->CTe->infCte->emit->IE;
                $dhEmi     = $xml->CTe->infCte->ide->dhEmi;
                $tpAmb     = $xml->CTe->infCte->ide->tpAmb;
                $cStat     = $xml->protCTe->infProt->cStat;

                switch ($cStat) {
                    case 100:
                        $cStat = 100;
                        break;
                    case 101:
                        $cStat = 101;
                        break;
                    case 135:
                        $cStat = 101;
                        break;
                    case 155:
                        $cStat = 101;
                        break;
                    case 150:
                        $cStat = 150;
                        break;
                    case 151:
                        $cStat = 151;
                        break;
                    case 110:
                        $cStat = 110;
                        break;
                    case 301:
                        $cStat = 110;
                        break;
                    case 302:
                        $cStat = 110;
                        break;
                    case 303:
                        $cStat = 110;
                        break;
                }

                $vNF = $xml->CTe->infCte->vPrest->vTPrest;

                if ($serie >= 920) {
                    $CNPJ = $xml->CTe->infCte->emit->CPF;
                } else {
                    $CNPJ = $xml->CTe->infCte->emit->CNPJ;
                }
                $razao_social = $xml->CTe->infCte->emit->xNome;
                $nome_fantasia = $xml->CTe->infCte->emit->xFant;
                $logradouro   = $xml->CTe->infCte->emit->enderEmit->xLgr;
                $numero   = $xml->CTe->infCte->emit->enderEmit->nro;

                $bairro   = $xml->CTe->infCte->emit->enderEmit->xBairro;
                $cep   = $xml->CTe->infCte->emit->enderEmit->CEP;
                $municipio   = $xml->CTe->infCte->emit->enderEmit->xMun;
                $uf   = $xml->CTe->infCte->emit->enderEmit->UF;
                $telefone   = $xml->CTe->infCte->emit->enderEmit->fone;
                //$email   = $xml->NFe->infNFe->emit->email;

                $data = str_replace('/', '-', $dhEmi);
                $mesano    = date('Ym', strtotime($data));
                $data_emissao  = date('Y-m-d', strtotime($dhEmi));

                // Define finalmente o nome
                $nameFile = "{$nameFile}";

                // Faz o upload para a pasta doc temporariamente
                $upload = $request->file->storeAs('docs', $nameFile);
                // pegar o caminho real onde foi feito o upload
                $url = Storage::url($upload);
                // Carrega o xml que acbou de ser salvo
                //$xml->load($url);

                // Novo endereço do upload
                $url_new = '/docs/' . $CNPJ . '/' . $IE . '/' . $mod . '/' . $mesano . '/' . $nameFile;

                if (Storage::exists($url_new)) {
                    Storage::delete($url_new);
                }

                // Storage::delete($upload);
                Storage::move($upload, $url_new);

                // aqui é a parte duplicada
                $empresas =  DB::table('companies')->Where('cnpj_cpf', '=', $CNPJ)->first();

                if (!$empresas) {

                    $campos = [
                        'cnpj_cpf' => $CNPJ,
                        'corporate_name' => mb_strtoupper($razao_social),
                        'fantasy_name' => mb_strtoupper($nome_fantasia),
                        //'email' => $email,
                        'public_place' => $logradouro,
                        'home_number' => $numero,
                        //'complement' => $complemento,
                        'district' => $bairro,
                        'zip_code' => $cep,
                        'county' => $municipio,
                        'uf' => $uf,
                        'phone_number' => $telefone
                    ];
                    DB::table('companies')->insert($campos);
                }

                //Apaga no banco de dados a linha do arquivo duplicado ou seja quando o arquivo e cancelado
                $docs =  DB::table('documents')->where('key', '=', $chNFe)->get();

                if (!empty($docs)) {
                    foreach ($docs as $doc) {
                        DB::table('documents')->where('id', '=', $doc->id)->delete();
                    }
                }

                $campos = [
                    'cnpj_cpf' => $CNPJ,
                    'ie' => $IE,
                    'model' => $mod,
                    'series' => $serie,
                    'number' => $nNF,
                    'key' => $chNFe,
                    'month_year' => $mesano,
                    'issue_dh' => $data_emissao,
                    'path_xml' => $url_new,
                    'protocol' => $nProt,
                    'environment_type' => $tpAmb,
                    'status_xml' => $cStat,
                    'vNF' => $vNF,
                    'size' => $size,
                ];

                DB::table('documents')->insert($campos);

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

    public function mdfe(Request $request)
    {
        if (isset($request->key) && $request->key == 'Sistema') {
            if ($request->hasFile('file') && $request->file('file')->isValid()) {

                // Recupera a extensão do arquivo
                $extension = $request->file->extension();
                $nameFile = $request->file->getClientOriginalName();
                $size = $request->file->getSize();

                $xml = simplexml_load_file($request->file);

                $nNF       = $xml->MDFe->infMDFe->ide->nMDF;
                $serie     = $xml->MDFe->infMDFe->ide->serie;
                $mod       = $xml->MDFe->infMDFe->ide->mod;
                $chNFe     = $xml->protMDFe->infProt->chMDFe;
                $nProt     = $xml->protMDFe->infProt->nProt;
                $IE        = $xml->MDFe->infMDFe->emit->IE;
                $dhEmi     = $xml->MDFe->infMDFe->ide->dhEmi;
                $tpAmb     = $xml->MDFe->infMDFe->ide->tpAmb;
                $cStat     = $xml->protMDFe->infProt->cStat;

                switch ($cStat) {
                    case 100:
                        $cStat = 100;
                        break;
                    case 101:
                        $cStat = 101;
                        break;
                    case 135:
                        $cStat = 101;
                        break;
                    case 155:
                        $cStat = 101;
                        break;
                    case 150:
                        $cStat = 150;
                        break;
                    case 151:
                        $cStat = 151;
                        break;
                    case 110:
                        $cStat = 110;
                        break;
                    case 301:
                        $cStat = 110;
                        break;
                    case 302:
                        $cStat = 110;
                        break;
                    case 303:
                        $cStat = 110;
                        break;
                }

                $vNF = $xml->MDFe->infMDFe->tot->vCarga;

                if ($serie >= 920) {
                    $CNPJ = $xml->MDFe->infMDFe->emit->CPF;
                } else {
                    $CNPJ = $xml->MDFe->infMDFe->emit->CNPJ;
                }
                $razao_social = $xml->MDFe->infMDFe->emit->xNome;
                $nome_fantasia = $xml->MDFe->infMDFe->emit->xFant;

                $logradouro   = $xml->MDFe->infMDFe->emit->enderEmit->xLgr;
                $numero   = $xml->MDFe->infMDFe->emit->enderEmit->nro;

                $bairro   = $xml->MDFe->infMDFe->emit->enderEmit->xBairro;
                $cep   = $xml->MDFe->infMDFe->emit->enderEmit->CEP;
                $municipio   = $xml->MDFe->infMDFe->emit->enderEmit->xMun;
                $uf   = $xml->MDFe->infMDFe->emit->enderEmit->UF;
                $telefone   = $xml->MDFe->infMDFe->emit->enderEmit->fone;
                //$email   = $xml->NFe->infNFe->emit->email;

                $data = str_replace('/', '-', $dhEmi);
                $mesano    = date('Ym', strtotime($data));
                $data_emissao  = date('Y-m-d', strtotime($dhEmi));

                // Define finalmente o nome
                $nameFile = "{$nameFile}";

                // Faz o upload para a pasta doc temporariamente
                $upload = $request->file->storeAs('docs', $nameFile);
                // pegar o caminho real onde foi feito o upload
                $url = Storage::url($upload);
                // Carrega o xml que acbou de ser salvo
                //$xml->load($url);

                // Novo endereço do upload
                $url_new = '/docs/' . $CNPJ . '/' . $IE . '/' . $mod . '/' . $mesano . '/' . $nameFile;

                if (Storage::exists($url_new)) {
                    Storage::delete($url_new);
                }

                // Storage::delete($upload);
                Storage::move($upload, $url_new);

                // aqui é a parte duplicada
                $empresas =  DB::table('companies')->Where('cnpj_cpf', '=', $CNPJ)->first();

                if (!$empresas) {

                    $campos = [
                        'cnpj_cpf' => $CNPJ,
                        'corporate_name' => mb_strtoupper($razao_social),
                        'fantasy_name' => mb_strtoupper($nome_fantasia),
                        //'email' => $email,
                        'public_place' => $logradouro,
                        'home_number' => $numero,
                        //'complement' => $complemento,
                        'district' => $bairro,
                        'zip_code' => $cep,
                        'county' => $municipio,
                        'uf' => $uf,
                        'phone_number' => $telefone
                    ];
                    DB::table('companies')->insert($campos);
                }

                //Apaga no banco de dados a linha do arquivo duplicado ou seja quando o arquivo e cancelado
                $docs =  DB::table('documents')->where('key', '=', $chNFe)->get();

                if (!empty($docs)) {
                    foreach ($docs as $doc) {
                        DB::table('documents')->where('id', '=', $doc->id)->delete();
                    }
                }

                $campos = [
                    'cnpj_cpf' => $CNPJ,
                    'ie' => $IE,
                    'model' => $mod,
                    'series' => $serie,
                    'number' => $nNF,
                    'key' => $chNFe,
                    'month_year' => $mesano,
                    'issue_dh' => $data_emissao,
                    'path_xml' => $url_new,
                    'protocol' => $nProt,
                    'environment_type' => $tpAmb,
                    'status_xml' => $cStat,
                    'vNF' => $vNF,
                    'size' => $size,
                ];

                DB::table('documents')->insert($campos);

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

    public function printInvoice($id)
    {
        $document = DB::table('documents')->where('id', '=', $id)->first();
        $file = storage_path('app') . $document->path_xml;

        if (!File::exists($file)) {
            abort(404);
        }

        $xml = file_get_contents($file);

        $fontDefault = 'arial';
        $credits = '';

        try {
            switch ($document->model) {
                case 55: // NF-e
                    $danfe = new Danfe($xml);
                    $danfe->debugMode(false);
                    $danfe->setDefaultFont($fontDefault);
                    $danfe->creditsIntegratorFooter($credits);
                    $pdf = $danfe->render();
                    break;

                case 57: // CT-e
                    $dacte = new Dacte($xml);
                    $dacte->debugMode(false);
                    $dacte->printParameters('P', 'A4', 2, 2);
                    $dacte->setDefaultFont($fontDefault);
                    $dacte->creditsIntegratorFooter($credits);
                    $dacte->setDefaultDecimalPlaces(2);
                    $pdf = $dacte->render();
                    break;

                case 58: // MDF-e
                    $damdfe = new Damdfe($xml);
                    $damdfe->debugMode(false);
                    $damdfe->setDefaultFont($fontDefault);
                    $damdfe->creditsIntegratorFooter($credits);
                    $pdf = $damdfe->render();
                    break;
					
				case 59: // CF-e Sat
                    $danfe = new Danfe($xml);
                    $danfe->debugMode(false);
                    $danfe->setDefaultFont($fontDefault);
                    $danfe->creditsIntegratorFooter($credits);
                    $pdf = $danfe->render();
                    break;	

                case 65: // NFC-e
                    $danfce = new Danfce($xml);
                    $danfce->debugMode(false);
                    $danfce->setPaperWidth(80);
                    $danfce->setMargins(2);
                    $danfce->setDefaultFont($fontDefault);
                    $danfce->setOffLineDoublePrint(true);
                    $danfce->creditsIntegratorFooter($credits);
                    $pdf = $danfce->render();
                    break;
            }

            header('Content-Type: application/pdf');
            ob_end_clean();
            echo $pdf;
        } catch (Exception $e) {
            echo "Ocorreu um erro durante o processamento :" . $e->getMessage();
        }
    }

    public function printEvent_cte($id)
    {
        $document = EventDocument::with('company')->where('id', '=', $id)->first();

        $file = storage_path('app') . $document->path_xml;

        if (!File::exists($file)) {
            abort(404);
        }

        $xml = file_get_contents($file);

        $fontDefault = 'arial';
        $credits = '';

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
