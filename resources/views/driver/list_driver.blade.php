@extends('layouts.application')

@section('content')
    <h1 class="ls-title-intro ls-ico-user-add">Listagem de Técnicos</h1>
    <div class="table-responsive">
        <div class="ls-box">
            <table class="ls-table ls-table-striped ls-bg-header">
                <thead>
                    <tr>
                        <th class="text-center">Nome do Técnico</th>
                        <th class="text-center">CPF</th>
                        <th class="text-center">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($drivers as $driver)
                        @if ($driver->name_driver != 'SEM MOTORISTA')
                            <tr>
                                <td class="text-center">{{ $driver->name_driver }}</td>
                                <td class="text-center">{{ $driver->cpf }}</td>
                                <td class="text-center">
                                    <a class="ls-ico-pencil ls-btn-dark" style="background-color: blue;"
                                        href="{{ route('driver.edit', $driver->id) }}" title="Editar"></a>

                                    <!-- Formulário para exclusão -->
                                    <form action="{{ route('driver.delete', $driver->id) }}" method="POST"
                                        style="display:inline;" onsubmit="return confirmDeletion();">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ls-ico-remove ls-btn-primary-danger"
                                            title="Excluir"></button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Script para confirmação de exclusão -->
    <script>
        function confirmDeletion() {
            return confirm('Você realmente deseja excluir este motorista?');
        }
    </script>
@stop
