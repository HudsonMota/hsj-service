<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    /* INDEX */
    public function index()
    {
        // Dados dos roteiros autorizados
        $scriptsauthorizeds = DB::table('authorizerequests')
            ->leftJoin('drivers', 'authorizerequests.driver', '=', 'drivers.id')
            ->leftJoin('vehiclerequests', 'authorizerequests.arr_requests_in_script', '=', 'vehiclerequests.id') // Join com vehiclerequests
            ->leftJoin('sectors', 'vehiclerequests.setorsolicitante', '=', 'sectors.cc')
            ->leftJoin('users', 'vehiclerequests.solicitante', '=', 'users.id')
            ->select(
                'authorizerequests.id as id',
                'authorized_departure_date as datasaida',
                'authorized_departure_time as horasaida',
                'authorizer',
                'statusauthorization as statussolicitacao',
                'drivers.name_driver as tecnico',
                'users.name as solicitante',
                'sectors.cc as setor_cc',
                'sectors.sector as setor_nome',
                'vehiclerequests.admfin as admfin',  // Incluindo o campo admfin de vehiclerequests
                DB::raw("'ATRIBUIDO' as status_type")
            )
            ->where('statusauthorization', 'ATRIBUIDO')
            ->orderBy('authorized_departure_date', 'desc')
            ->orderBy('authorized_departure_time', 'desc')
            ->get();


        // Dados das solicitações pendentes
        $vehiclerequests = DB::table('vehiclerequests')
            ->leftJoin('sectors', 'vehiclerequests.setorsolicitante', '=', 'sectors.cc') // Junta a tabela sectors
            ->leftJoin('users', 'vehiclerequests.solicitante', '=', 'users.id') // Junta a tabela users para obter o nome do solicitante
            ->select(
                'vehiclerequests.id as id',
                'vehiclerequests.admfin as admfin',
                // 'vehiclerequests.admfin as adm_fin',
                'datasaida as datasaida',
                'horasaida as horasaida',
                'sectors.cc as setor_cc', // Exibe o setor cc
                'sectors.sector as setor_nome', // Exibe o nome do setor
                'users.name as solicitante', // Nome do solicitante
                DB::raw("NULL as tecnico"), // Não inclui o nome do técnico para pendentes
                DB::raw("'PENDENTE' as status_type")
            )
            ->where('statussolicitacao', 'PENDENTE')
            ->orderBy('datasaida', 'desc')
            ->orderBy('horasaida', 'desc')
            ->get();

        // Combina os dados
        $combinedData = $scriptsauthorizeds->concat($vehiclerequests)
            ->sort(function ($a, $b) {
                // Ordena os dados pela data e hora decrescente
                return ($b->datasaida <=> $a->datasaida) ?: ($b->horasaida <=> $a->horasaida);
            });

        // Pagina os dados combinados
        $perPage = 10; // Número de itens por página
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $combinedData->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedData = new LengthAwarePaginator($currentItems, $combinedData->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('dashboard', ['combinedData' => $paginatedData]);
    }
    /* /INDEX */

    public function endScript(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');

        // Atualize o status do roteiro conforme necessário
        // Exemplo:
        DB::table('authorizerequests')
            ->where('id', $id)
            ->update(['statusauthorization' => $status]);

        return redirect()->back()->with('success', 'Status atualizado com sucesso.');
    }

    public function getTotalCount()
    {
        $total = DB::table('combinedData')->count(); // Ajuste conforme necessário
        return response()->json(['total' => $total]);
    }
}
