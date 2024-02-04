<table id="tblusuario" name="tblusuario" class="table table-striped table-inverse table-responsive">
    <caption>Servicios</caption>
    <thead class="thead-inverse">
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Api</th>
            <th>Descripcion</th>
            <th>Precio</th>
            <th>imagen</th>

            <th>Estatus</th>
            <th>Opcion</th>
        </tr>
    </thead>
    <tbody>
        @foreach (App\Models\Servicio::all() as $servicio)
        @if($servicio->estatus!=0)
        <tr>
            <td>{{$servicio->nombre}}</td>
            <td>{{$servicio->tipo}}</td>
            <td>{{$servicio->api}}</td>
            <td>{{$servicio->descripcion}}</td>
            <td>{{$servicio->precio}}</td>
            <td>@if (session('imagen'))
                <img src="{{ asset('storage/' . session('imagen')) }}" alt="Imagen Subida">
              @else
              <img src="{{ asset('storage/' . $servicio->imagen)  }}" alt="">

                @endif</td>
            <td>
                @if($servicio->estatus==1)
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                    fill="currentColor" class="bi bi-check-square-fill text-success"
                    viewBox="0 0 16 16">
                    <path
                        d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z" />
                </svg>
                @elseif($servicio->estatus==0)
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                    fill="currentColor" class="bi bi-exclamation-octagon-fill text-danger"
                    viewBox="0 0 16 16">
                    <path
                        d="M11.46.146A.5.5 0 0 0 11.107 0H4.893a.5.5 0 0 0-.353.146L.146 4.54A.5.5 0 0 0 0 4.893v6.214a.5.5 0 0 0 .146.353l4.394 4.394a.5.5 0 0 0 .353.146h6.214a.5.5 0 0 0 .353-.146l4.394-4.394a.5.5 0 0 0 .146-.353V4.893a.5.5 0 0 0-.146-.353L11.46.146zM8 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                </svg>
                @endif
            </td>
            <td class="btn btn-group-lg">
                <a data-id="{{$servicio->id}}" data-nombre="{{$servicio->nombre}}" data-tipo="{{$servicio->tipo}}"
                    data-api="{{$servicio->api}}" data-estatus="{{$servicio->estatus}}"  data-precio="{{$servicio->precio}}"  data-descripcion ="{{$servicio->descripcion}}" data-bs-toggle="modal" data-bs-target="#modal_editar_usuario"
                    class="btn btn-outline-warning " href=""><svg xmlns="http://www.w3.org/2000/svg" width="16"
                        height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                        <path
                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                        <path fill-rule="evenodd"
                            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                    </svg></a>

                <a  class="btn btn-outline-danger "
                    href="/eliminar_usuario/{{$servicio->id}}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-trash3-fill" viewBox="0 0 16 16">
                        <path
                            d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5" />
                    </svg></a>

            </td>
        </tr>
        @endif
        @endforeach
    </tbody>
</table>
