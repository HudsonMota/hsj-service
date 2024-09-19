<!-- ################################# H O M E ############################################# -->
@extends('layouts.application')
@section('content')
    <?php
    $name = substr(Auth::user()->name, 0, strrpos(substr(Auth::user()->name, 0, 20), ' '));
    ?>
    <h1 class="ls-title-intro ls-ico-home">
        Olá {{ strtok(Auth::user()->name, ' ') }}! Seja bem-vindo ao {{ config('app.name') }}
    </h1>


    {{-- MÊS ATUAL --}}
    <div class="ls-box ls-board-box">
        <header class="ls-info-header card-footer">
            <h2 class="ls-title-3"><b>RELATÓRIO MÊS ATUAL &nbsp; &nbsp; {{ date('m/Y') }}</b></h2>
        </header>

        <!-- ATENDIMENTOS PENDENTES -->
        @can('View ADM dashboard')
            <div class="col-sm-6 col-md-3 dash">
                <div class="ls-box" style="background-color: #f14848;">
                    <div class="ls-box-head">
                        <h6 class="ls-title-4" style="color: #000; font-weight: bold;">ATENDIMENTOS PENDENTES</h6>
                    </div>
                    <div class="ls-box-body">
                        <strong>{{ App\Solicitacao::where('statussolicitacao', 'PENDENTE')->where('created_at', '>', date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y'))))->count() }}</strong>
                    </div>
                    <div class="ls-box-footer">
                        <div class="box-header">
                            <a href="{{ route('authorizations') }}" class="btn btn-light"
                                style="width: 150px; margin-left: auto; margin-right: auto; color: #000; font-weight: bold;">Exibir</a>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        <!-- ATENDIMENTOS FINALIZADOS -->
        @can('View ADM dashboard')
            <div class="col-sm-6 col-md-3 dash">
                <div class="ls-box" style="background-color: #36eb7b;">
                    <div class="ls-box-head">
                        <h6 class="ls-title-4" style="color: #000; font-weight: bold;">ATENDIMENTOS FINALIZADOS</h6>
                    </div>
                    <div class="ls-box-body">
                        <strong>{{ App\Authorizacao::where('statusauthorization', 'REALIZADO')->where('authorized_departure_date', '>', date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y'))))->count() }}</strong>
                    </div>
                    <div class="ls-box-footer">
                        <div class="box-header">
                            <a href="{{ route('authorizations') }}" class="btn btn-light"
                                style="width: 150px; margin-left: auto; margin-right: auto; color: #000; font-weight: bold;">Exibir</a>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    <div class="ls-box ls-board-box">
        <header class="ls-info-header card-footer">
            <h2 class="ls-title-3"><b>RELATÓRIO COMPLETO</b></h2>
        </header>

        <!-- ATENDIMENTOS PENDENTES -->
        @can('View ADM dashboard')
            <div class="col-sm-6 col-md-3 dash">
                <div class="ls-box" style="background-color: #F7293D;">
                    <div class="ls-box-head">
                        <h6 class="ls-title-4" style="color: #000; font-weight: bold;">ATENDIMENTOS PENDENTES</h6>
                    </div>
                    <div class="ls-box-body">
                        <strong>{{ App\Solicitacao::where('statussolicitacao', 'PENDENTE')->count() }}</strong>
                    </div>
                    <div class="ls-box-footer">
                        <div class="box-header">
                            <a href="{{ route('authorizations') }}" class="btn btn-light"
                                style="width: 150px; margin-left: auto; margin-right: auto; color: #000; font-weight: bold;">Exibir</a>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        <!-- ATENDIMENTOS FINALIZADOS -->
        @can('View ADM dashboard')
            <div class="col-sm-6 col-md-3 dash">
                <div class="ls-box" style="background-color: 	#36eb7b;">
                    <div class="ls-box-head">
                        <h6 class="ls-title-4" style="color: #000; font-weight: bold;">ATENDIMENTOS FINALIZADOS</h6>
                    </div>
                    <div class="ls-box-body">
                        <strong>{{ App\Solicitacao::where('statussolicitacao', 'REALIZADO')->count() }}</strong>
                    </div>
                    <div class="ls-box-footer">
                        <div class="box-header">
                            <a href="{{ route('solicitacoes.realizadas') }}" class="btn btn-light"
                                style="width: 150px; margin-left: auto; margin-right: auto; color: #000; font-weight: bold;">Exibir</a>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        <!-- TÉCNICOS CADASTRADOS -->
        @can('View ADM dashboard')
            <div class="col-sm-6 col-md-3 dash">
                <div class="ls-box" style="background-color: #b3a961;">
                    <div class="ls-box-head">
                        <h6 class="ls-title-4" style="color: #000; font-weight: bold;">TÉCNICOS CADASTRADOS</h6>
                    </div>
                    <div class="ls-box-body">
                        <strong>{{ App\Driver::all()->count() - 1 }}</strong>
                    </div>
                    <div class="ls-box-footer">
                        <div class="box-header">
                            <a href="{{ route('drivers') }}" class="btn btn-light"
                                style="width: 150px; margin-left: auto; margin-right: auto;color: #000; font-weight: bold;">Exibir</a>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endsection
