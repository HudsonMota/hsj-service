<?php

namespace App\Http\Controllers;

date_default_timezone_set('America/Fortaleza');

use App\Http\Requests\AuthorizeRequest;
use App\Authorizacao;
use App\Sector;
use App\Solicitacao;
use App\User;
// use App\Adress;
use App\Vehicle;
use Dotenv\Result\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Rfc4122\UuidV1;
use SebastianBergmann\Environment\Console;

class AuthorizationController extends Controller
{
    // Função para exibir view de criação de roteiros
    public function index(Request $field)
    {
        $users = User::get();
        // $adress = Adress::get();

        $vehiclerequests = Solicitacao::where('statussolicitacao', 'PENDENTE')->orderby('datasaida', 'desc')->paginate(10);
        return view('authorization/authorization-add', compact('vehiclerequests'), compact('users')/*, compact('adress')*/);
    }

    // Função para rejeitar solicitação
    public function reject(Request $request)
    {
        $requests = $request->input('requests');

        if (!is_array($requests)) {
            return response()->json(['success' => false], 400);
        }

        DB::table('vehiclerequests')
            ->whereIn('id', $requests)
            ->update(['statussolicitacao' => 'RECUSADO']);

        return response()->json(['success' => true]);
    }


    // Função para gravar novo roteiro      // AUTHORIZATION LIST
    public function store(Request $field)
    {

        $authorizerequest = new Authorizacao();
        $authorizerequest->driver = $field['driver'];
        $authorizerequest->vehicle = $field['vehicle'];
        $authorizerequest->authorized_departure_date  = $field['datasaidaautorizada'];
        $authorizerequest->authorized_departure_time = $field['horasaidaautorizada'];

        $authorizerequest->acompanhamento = $field['acompanhamento'];

        //JUSTIFICATIVA
        $authorizerequest->justificativa = $field['justificativa'];

        if (is_null($field['kmfinal'])) {
        } else {
            $authorizerequest->return_date  = $field['dataretorno'];
            $authorizerequest->return_time = $field['horaretorno'];
            $authorizerequest->output_mileage  = $field['kminicial'];
            $authorizerequest->return_mileage = $field['kmfinal'];
        }

        // Salva o usuário logado que está autorizando o roteiro
        $authorizerequest->authorizer = Auth::user()->name;

        $authorizerequest->statusauthorization = 'ATRIBUIDO';      //TYPE ENUM

        // Pega a string contendo os Id's das solicitações selecionadas e converte em um array
        $selectrequestsarray = $field['selectrequestsarray']; //"101, 102, 103"
        $selectrequestexplode = explode(",", $selectrequestsarray); //"[101, 102, 103]"

        // Identificador de roteiro
        $date = date("d-m-Y");
        $time = date("H:i:s");
        $cod = md5(uniqid(rand(), true));
        $cod_group = $date . "_" . $time . "_" . $cod;


        // $cod_group = UuidV1::uuid4();        gera o identificador do roteiro

        // A partir do array $selectrequestexplode, cada solicitação receberá um novo update
        for ($i = 0; $i < count($selectrequestexplode); $i++) {
            // Salva um código identificador de roteiro
            DB::table('vehiclerequests')
                ->where('id', $selectrequestexplode[$i])
                ->update(
                    [
                        'grouprequest' => $cod_group,
                        'statussolicitacao' => "ATRIBUIDA",
                    ]
                );
        }

        $authorizerequest->arr_requests_in_script = $selectrequestsarray;

        $authorizerequest->itinerary = $cod_group;
        if ($selectrequestsarray) {
            $authorizerequest->save();

            return redirect()->route('authorizations');
        } else {
            return redirect()->back()->with('error', 'Não é possível criar roteiro vazio! Por favor adcione uma solicitação ao roteiro.');
        }
    }

    // Função para visualizar dados da VIEW /authorizations
    public function list_authorizations()
    {
        // // Retorna os setores
        $sectors = DB::table('sectors')->get();

        // Retorna os roteiros em ordem decrescente
        $scriptsauthorizeds = DB::table('authorizerequests')->orderBy('id', 'desc')->paginate(10);

        // // Retorna os motoristas
        $drivers = DB::table('drivers')->get();

        return view('authorization/authorization-list', compact('scriptsauthorizeds', 'sectors', /*'vehicles',*/ 'drivers'));
    }

    // Função para exbir view de edição de formulário
    public function get_edit_authorization($id)
    {
        $users = User::get();
        $scriptauthorized = Authorizacao::find($id);
        $vehiclerequests = Solicitacao::where('statussolicitacao', '=', 'PENDENTE')->orderby('created_at', 'desc')->paginate(10);

        $itinerary = $scriptauthorized->itinerary;
        $grouprequestsauth = Solicitacao::where('grouprequest', '=', $itinerary)->orderby('created_at', 'desc')->get();

        return view('authorization/authorization-edit', compact('vehiclerequests', 'scriptauthorized', 'grouprequestsauth', 'users'));
    }

    // Função para editar informações
    public function post_edit_authorization(Request $info, $id)
    {
        $authorizerequest = Authorizacao::find($id);

        // Este trecho insere nulo no campo grouprequest da tabela de solicitações
        // a partir do roteiro informado
        DB::table('vehiclerequests')
            ->where('grouprequest', $authorizerequest->itinerary)
            ->update(
                [
                    'grouprequest' => null,
                    'statussolicitacao' => "PENDENTE",
                ]
            );

        $authorizerequest->driver = $info['driver'];
        $authorizerequest->vehicle = $info['vehicle'];
        $authorizerequest->authorized_departure_date  = $info['datasaidaautorizada'];
        $authorizerequest->authorized_departure_time = $info['horasaidaautorizada'];

        // Salva o usuário logado que está autorizando o roteiro
        $authorizerequest->authorizer = Auth::user()->name;

        $authorizerequest->statusauthorization = 'ATRIBUIDO';

        // Pega a string contendo os Id's das solicitações selecionadas e converte em um array
        $selectrequestsarray = $info['selectrequestsarray'];
        $selectrequestexplode = explode(",", $selectrequestsarray);

        // Cria uma string unica para Identificador de roteiro
        $date = date("d-m-Y");
        $time = date("H:i:s");
        $cod = md5(uniqid(rand(), true));
        $cod_group = $date . "_" . $time . "_" . $cod;

        // A partir do array $selectrequestexplode, cada solicitação receberá um novo update
        for ($i = 0; $i < count($selectrequestexplode); $i++) {
            // Salva um código identificador de roteiro
            DB::table('vehiclerequests')
                ->where('id', $selectrequestexplode[$i])
                ->update(
                    [
                        'grouprequest' => $cod_group,
                        'statussolicitacao' => "ATRIBUIDA",
                    ]
                );
        }

        $authorizerequest->arr_requests_in_script = $selectrequestsarray;

        $authorizerequest->itinerary = $cod_group;

        if ($selectrequestsarray) {
            $authorizerequest->save();

            return redirect()->route('authorizations');
        } else {
            DB::table('authorizerequests')
                ->where('id', $authorizerequest->id)
                ->delete();
            return redirect()->route('authorizations')->with('error', 'Não é possível criar roteiro vazio! Por favor adcione uma solicitação ao roteiro.');
        }
    }

    // Função para finalizar roteiro
    function end_script(Request $info)
    {
        // Identificando o roteiro a ser finalizado
        $authorizerequest = Authorizacao::find($info['id']);

        // Salvando informações do formulário
        $authorizerequest->return_date = $info['dataretorno'];
        $authorizerequest->return_time = $info['horaretorno'];
        // $authorizerequest->output_mileage   = $info['kminicial'];
        // $authorizerequest->return_mileage = $info['kmfinal'];
        $authorizerequest->authorizer = Auth::user()->name;
        $authorizerequest->acompanhamento = $info['acompanhamento'];
        $authorizerequest->statusauthorization = "REALIZADO";

        //JUSTIFICATIVA
        $authorizerequest->justificativa = $info['justificativa'];

        // Caso mutiplas roteiros variável pegará a string que registra os ids das solicitações
        // $authorizerequest->arr_requests_in_script e fará um explode() para transformará em array
        $arr_requests_in_script = explode(',', $authorizerequest->arr_requests_in_script);

        for ($i = 0; $i < count($arr_requests_in_script); $i++) {
            Solicitacao::where('grouprequest', $authorizerequest->itinerary)
                ->where('id', $arr_requests_in_script[$i])
                ->update(
                    [
                        // 'mileage_traveled' => $totalToRq[$i],
                        'statussolicitacao' => "REALIZADO",
                        'created_at' => $info['dataretorno'] . ' ' . $info['horaretorno'], //Gerado automáticamente
                        'updated_at' => $info['dataretorno'] . ' ' . $info['horaretorno'], //Gerado automáticamente
                    ]
                );
        }

        // dd($authorizerequest);
        $authorizerequest->save();


        return redirect()->route('authorizations');
    }

    // Função deletar Autorização
    public function delete_authorization($id)
    {
        $authorizerequest = Authorizacao::find($id);

        Solicitacao::where('grouprequest', $authorizerequest->itinerary)
            ->update(
                [
                    'grouprequest' => null,
                    'statussolicitacao' => "PENDENTE",
                ]
            );

        $authorizerequest->delete();
        return redirect()->back();
    }
}
