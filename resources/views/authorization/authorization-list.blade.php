@extends('layouts.application')

@section('content')
    @if (!empty($successMsg))
        <div class="alert alert-danger">{{ $errorMsg }}</div>
    @endif

    @if (\Session::has('error'))
        <div class="ls-modal ls-opened" id="myAwesomeModal" role="dialog" aria-hidden="false" aria-labelledby="lsModal1"
            tabindex="-1">
            <div class="ls-modal-box">
                <div class="ls-modal-header">
                    <h2 class="ls-modal-title" id="lsModal1"><strong>Atenção</strong></h2>
                </div>
                <div class="ls-modal-body">
                    <h3 class="alert alert-danger">{!! \Session::get('error') !!}</h3>
                </div>
                <div class="ls-modal-footer">
                    <button onclick="closeModal()" style="margin-bottom: 20px;"
                        class="btn btn-danger ls-float-right">Fechar</button>
                </div>
            </div>
        </div>
    @endif

    <script>
        function closeModal() {
            locastyle.modal.close();
        }
    </script>

    <h1 class="ls-title-intro ls-ico-code">Solicitações e Roteiros</h1>

    <div class="col-md-12">
        <b class="ls-title-5">
            <span style="color: green;">
                {{ DB::table('vehiclerequests')->where('statussolicitacao', 'AUTORIZADA')->count() }}
            </span> - Solicitações autorizadas
        </b>
        <hr>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="">Cod</th>
                    <th width="300">Técnico</th>
                    <th width="200">Saída <br> Retorno</th>
                    <th width="300">Autorizador</th>
                    <th>Status</th>
                    <th width="100">Ações</th>
                </tr>
            </thead>
            <tbody id="tbodyright">
                @foreach ($scriptsauthorizeds as $scriptauthorized)
                    <tr data-index="{{ $scriptauthorized->id }}">
                        <td>{{ $scriptauthorized->id }}</td>
                        <td>
                            @foreach ($drivers as $driver)
                                @if ($driver->id === $scriptauthorized->driver)
                                    {{ $driver->name_driver }}
                                @endif
                            @endforeach
                        </td>
                        <td>
                            {{ date('d/m/Y', strtotime($scriptauthorized->authorized_departure_date)) }}<br>
                            {{ $scriptauthorized->authorized_departure_time }} <br>
                        </td>
                        <td>
                            {{ $scriptauthorized->authorizer }}
                        </td>
                        <td>
                            @if ($scriptauthorized->statusauthorization == 'ATRIBUIDO')
                                <b><span style="color: green;">{{ $scriptauthorized->statusauthorization }}</span></b>
                            @elseif($scriptauthorized->statusauthorization == 'PENDENTE')
                                <b><span style="color: red;">{{ $scriptauthorized->statusauthorization }}</span></b>
                            @elseif($scriptauthorized->statusauthorization == 'REALIZADO')
                                <b><span
                                        style="color: Mediumaquamarine;">{{ $scriptauthorized->statusauthorization }}</span></b>
                            @elseif($scriptauthorized->statusauthorization == 'NÃO REALIZADO')
                                <b><span style="color: blue;">{{ $scriptauthorized->statusauthorization }}</span></b>
                            @endif
                        </td>
                        <td>
                            @if ($scriptauthorized->statusauthorization == 'REALIZADO')
                                <div class="col-md-12">
                                    <a class="ls-ico-windows ls-btn" style="margin: 2px;"
                                        href="/authorization-pdf/{{ $scriptauthorized->id }}" target="_blank"></a>
                                </div>
                            @else
                                {{-- <div class="col-md-6">
                                    <a class="ls-ico-pencil ls-btn-dark" style="background-color: blue; margin: 2px;"
                                        href="/authorization-edit/{{ $scriptauthorized->id }}"></a>
                                </div> --}}
                                <div class="col-md-6">
                                    <a onclick="endScript('{{ $scriptauthorized->id }}', '{{ $scriptauthorized->itinerary }}')"
                                        class="ls-ico-checkbox-checked ls-btn-primary" style="margin: 2px;"></a>
                                </div>
                                <div class="col-md-6">
                                    <a class="ls-ico-remove ls-btn-primary-danger" style="margin: 2px;"
                                        href="/authorization/delete/{{ $scriptauthorized->id }}"></a>
                                </div>
                                <div class="col-md-6">
                                    <a class="ls-ico-windows ls-btn" style="margin: 2px;"
                                        href="/authorization-pdf/{{ $scriptauthorized->id }}" target="_blank"></a>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 5px;" class="ls-float-right">
            <a onclick="resetScript()" class="ls-btn-primary ls-ico-spinner">Mostrar todos os roteiros</a>
        </div>

        {{ $scriptsauthorizeds->links() }}
    </div>

    <script type="text/javascript">
        function endScript(id, itinerary) {
            $('#id').val(id);
            locastyle.modal.open("#modalLarge");

            var arr = [];

            function countRequestInScript(scriptAuth) {
                if (scriptAuth.id == id) {
                    function countRequest(requestAuth) {
                        if (requestAuth.grouprequest == scriptAuth.itinerary && scriptAuth.arr_requests_in_script.indexOf(
                                requestAuth.id) > -1) {
                            arr.push(requestAuth.id);
                        }
                    }
                    requests.forEach(countRequest);
                }
            }
            scriptauthorized.forEach(countRequestInScript);
        }
    </script>

    <div class="ls-modal" id="modalLarge">
        <div class="ls-modal-large">
            <div class="ls-modal-header">
                <button data-dismiss="modal">&times;</button>
                <h4 class="ls-modal-title">Finalizar Solicitação</h4>
            </div>
            <div class="ls-modal-body">
                <form method="POST" action="{{ url()->current() }}" class="ls-form row" id="add">
                    @csrf
                    <input type="hidden" id="id" name="id">
                    <fieldset id="field01">
                        <div class="col-md-12">
                            <div class="ls-box">
                                <div class="col-md-6">
                                    <b class="ls-label-text">Data de retorno</b>
                                    <input type="date" class="form-control" name="dataretorno"
                                        value="{{ date('Y-m-d') }}" required
                                        oninvalid="this.setCustomValidity('Informe a data de retorno do veículo!')"
                                        onchange="try{setCustomValidity('')}catch(e){}">
                                </div>
                                <div class="form-group col-md-6">
                                    <b class="ls-label-text">Hora de retorno</b>
                                    <input type="time" class="form-control" name="horaretorno"
                                        value="{{ date('H:i:s') }}" required
                                        oninvalid="this.setCustomValidity('Informe o horário de retorno do veículo!')"
                                        onchange="try{setCustomValidity('')}catch(e){}">
                                </div>
                                <div class="form-group col-md-12">
                                    <b class="ls-label-text">Acompanhamento</b>
                                    <textarea class="form-control" name="acompanhamento" rows="1" required
                                        oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px';"
                                        oninvalid="this.setCustomValidity('Detalhe o Acompanhamento do Atendimento!')"
                                        onchange="try{setCustomValidity('')}catch(e){}"></textarea>
                                </div>

                                {{-- <div class="form-group col-md-6" style="margin-bottom: 20px;">
                                    <b class="ls-label-text" style="color: red;">Quilometragem inicial</b>
                                    <input type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57'
                                        class="form-control" id="kminicial" name="kminicial" maxlength="6"
                                        autocomplete="off" required
                                        oninvalid="this.setCustomValidity('Informe a quilometragem inicial!')"
                                        onchange="try{setCustomValidity('')}catch(e){}">
                                </div> --}}
                                {{-- <div class="form-group col-md-6">
                                    <b class="ls-label-text" style="color: red;">Quilometragem final</b>
                                    <input type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57'
                                        class="form-control" id="kmfinal" name="kmfinal" maxlength="6" autocomplete="off"
                                        required>
                                    <script>
                                        function kmValidation() {
                                            var kminicial = document.getElementById("kminicial");
                                            var kmfinal = document.getElementById("kmfinal");
                                            var result = kmfinal.value - kminicial.value;
                                            if (result <= 0) {
                                                alert("Menor");
                                                kmfinal.setCustomValidity("A quilometragem final precisa ser maior que a inicial");
                                            } else {
                                                kmfinal.setCustomValidity("");
                                            }
                                            if (result >= 251) {
                                                kmfinal.setCustomValidity(
                                                    "Valor acima do limite! Verifique se o valor está correto ou entre em contato com o suporte.");
                                            }
                                        }
                                    </script>
                                    <!--Justificativa-->
                                    <div class="text-left">
                                        <h4>
                                            Justificativa? &nbsp;
                                            <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                        </h4>
                                        <input type="text" id="justificativa" name="justificativa">
                                    </div>
                                    <!--/Justificativa-->
                                </div> --}}
                            </div>
                        </div>
                        <div>
                            <button style="margin: 20px;" class="btn btn-danger ls-float-right" data-dismiss="modal"
                                type="button">Cancelar</button>
                            <button onclick="kmValidation()" style="margin: 20px;" type="submit"
                                class="ls-btn-primary">Salvar roteiro</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
@endsection
