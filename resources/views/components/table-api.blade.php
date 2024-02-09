<table id="tblusuario" name="tblusuario" class="table table-striped table-inverse table-responsive">
    <caption>API</caption>
    <thead class="thead-inverse">
        <tr>
            <th>Opcion</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Descripcion</th>
            <th>Estatus</th>
            <th>URL</th>
            <th>Response OK</th>
            <th>Response Fail</th>
            <th>Method</th>
            <th>JSON</th>
        </tr>
    </thead>
    <tbody>
        @foreach (App\Models\API::all() as $api)
        @if($api->estatus!=0)
        <tr>
            <td class="btn btn-group-lg">
                <a data-id_api="{{$api->id}}" data-url="{{$api->url}}" data-method="{{$api->method}}" data-json="{{$api->json}}" data-response_fail="{{$api->response_fail}}" data-response_ok="{{$api->response_ok}}" data-nombre="{{$api->nombre}}" data-tipo="{{$api->tipo}}"
                    data-descripcion="{{$api->descripcion}}" data-estatus="{{$api->estatus}}" data-bs-toggle="modal" data-bs-target="#modal_editar_usuario"
                    class="btn btn-outline-warning " href=""><svg xmlns="http://www.w3.org/2000/svg" width="16"
                        height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                        <path
                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                        <path fill-rule="evenodd"
                            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                    </svg></a>

                <a  class="btn btn-outline-danger "
                    href="/eliminar_usuario/{{$api->id}}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-trash3-fill" viewBox="0 0 16 16">
                        <path
                            d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5" />
                    </svg></a>

            </td>
            <td>{{$api->nombre}}</td>
            <td>{{$api->tipo}}</td>
            <td>{{$api->descripcion}}</td>
            <td>{{$api->estatus}}</td>
            <td>{{$api->url}}</td>
            <td>{{$api->response_ok}}</td>
            <td>{{$api->response_fail}}</td>
            <td>{{$api->method}}</td>
            <td>{{$api->json}}</td>
        </tr>
        @endif
        @endforeach
    </tbody>
</table>
