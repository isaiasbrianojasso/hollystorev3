<table id="tblusuario" name="tblusuario" class="table table-striped table-inverse table-responsive">
    <caption>Usuarios</caption>
    <thead class="thead-inverse">
        <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Opcion</th>
        </tr>
    </thead>
    <tbody>
        @foreach (App\Models\User::all() as $usuario)
        @if($usuario->estatus!=0)
        <tr>
            <td>{{$usuario->name}}</td>
            <td>{{$usuario->email}}</td>
            <td>@php
                $rol = App\Models\Rol::where('id', $usuario->rol_id)->get('nombre')->first();
                @endphp
                @php $plan = App\Models\Plan::where('id', $usuario->plan_id)->get('nombre')->first();
                @endphp

                {{$rol->nombre}} <br> <span class="badge text-bg-primary"> {{$plan->nombre}}</span> <br> <span class="badge text-bg-primary">$ {{$usuario->creditos}}</span>
                </td>
            <td class="btn btn-group-lg">
                <a data-id_usuario="{{$usuario->id}}" data-email="{{$usuario->email}}" data-name="{{$usuario->name}}"
                    data-rol_id="{{$usuario->rol_id}}" data-plan_id="{{$usuario->plan_id}}" data-bs-toggle="modal" data-bs-target="#modal_editar_usuario"
                    class="btn btn-outline-warning " href=""><svg xmlns="http://www.w3.org/2000/svg" width="16"
                        height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                        <path
                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                        <path fill-rule="evenodd"
                            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                    </svg></a>
                <a href="/admin/creditos/show/{{$usuario->id}}" type="button" class="btn btn-outline-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-currency-dollar" viewBox="0 0 16 16">
                        <path
                            d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718H4zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73l.348.086z">
                        </path>
                    </svg></a>
                <a  class="btn btn-outline-danger "
                    href="/eliminar_usuario/{{$usuario->id}}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
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
