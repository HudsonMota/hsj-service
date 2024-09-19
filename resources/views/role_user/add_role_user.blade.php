@extends('layouts.application')
@section('content')
    <h1 class="ls-title-intro ls-ico-cog">Gerenciar papeis de usuários</h1>
    <div class="ls-box">
        <hr>
        <h5 class="ls-title-5">Cadastrar Papeis de usuário:</h5>
        <form method="POST" action="{{ route('roleuser.postAdd') }}" class="ls-form row">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <fieldset>

                <div class="col-md-12">
                    <div class="row">
                        <!-- Seleção de Usuário -->
                        <div class="form-group col-md-4">
                            <label class="ls-label col-md-12">
                                <b class="ls-label-text">Usuário</b>
                                <div class="ls-custom-select">
                                    <select name="user_id" class="ls-select form-control">
                                        <option value="" disabled selected>Selecione um usuário</option>
                                        @foreach ($read as $call)
                                            <option value="{{ $call->id }}">{{ $call->id }} - {{ $call->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </label>
                        </div>

                        <!-- Seleção de Papel -->
                        <div class="form-group col-md-4">
                            <label class="ls-label col-md-12">
                                <b class="ls-label-text">Papel</b>
                                <div class="ls-custom-select">
                                    <select name="role_id" class="ls-select form-control">
                                        <option value="" disabled selected>Selecione um papel</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </label>
                        </div>

                        <!-- Descrição dos Papéis -->
                        <div class="form-group col-md-4">
                            <b class="ls-label-text">Descrição dos Papéis</b>
                            <div class="ls-label col-md-12">
                                <ul class="list-unstyled">
                                    <li><strong>SUPER ADM:</strong> Infraestrutura, administrador geral. Nível 1.</li>
                                    <li><strong>ADM:</strong> Serviços Gerais, administrador de frota. Nível 2.</li>
                                    <li><strong>MANAGER:</strong> Gestores Administrativos, ADM Financeiro, ADM Custos, etc.
                                        Nível 3.</li>
                                    {{-- <li><strong>PAD:</strong> Programa de Atendimento Domiciliar.</li> --}}
                                    <li><strong>USER REQUEST:</strong> Gestores de setor ou responsáveis autorizados. Nível
                                        4.</li>
                                    <li><strong>NO ROLE:</strong> Usuário sem permissões.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <div class="ls-actions-btn" style="border-top-color: rgba(255,255,255);">
                <input type="submit" value="Cadastrar" class="ls-btn-primary" title="Cadastrar" style="font-weight: bold;">
                <input type="reset" value="Limpar" class="ls-btn-primary-danger" sstyle="font-weight: bold;">
            </div>
        </form>
        <hr>
    </div>

@stop
