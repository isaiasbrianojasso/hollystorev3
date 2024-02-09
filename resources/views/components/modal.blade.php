<!-- Button trigger modal -->
<!-- Modal -->
<div class="modal fade" id="modal_agregar_usuario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Agregar Usuario</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/agregar_usuario">
                    @method('POST')
                    @csrf
                    <label for="">Nombre</label>
                    <input class="form-control" type="text" name="name" id="">
                    <label for="">Correo</label>
                    <input class="form-control" type="text" name="email" id="">
                    <label for="">Rol</label>
                    <select class="form-control" name="rol_id" id="">
                        @foreach (App\Models\Rol::all() as $rol)
                        <option value="{{$rol->id}}">{{$rol->nombre}}</option>
                        @endforeach
                    </select>

                    <label for="">Plan</label>
                    <select class="form-control" name="plan_id" id="">
                        @foreach (App\Models\Plan::all() as $plan)
                        <option value="{{$plan->id}}">{{$plan->nombre}}</option>
                        @endforeach
                    </select>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button></form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_editar_usuario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Editar Usuario</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/editar_usuario">
                    @method('POST')
                    @csrf
                    <label for="">Nombre</label>
                    <input class="form-control" type="text" name="name" id="name" value="">
                    <label for="">Correo</label>
                    <input class="form-control" type="text" name="email" id="email" value="">
                    <label for="">Password (Dejar vacio si no quieres cambiar la contraseña)</label>
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2"><svg xmlns="http://www.w3.org/2000/svg"
                                width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path
                                    d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg></span>
                        <input type="text" class="form-control" id="password" name="password"
                            aria-describedby="inputGroupPrepend2">
                    </div><label for="">Rol</label>
                    <input type="hidden" id="id_usuario" name="id_usuario">
                    <select class="form-control rol_id" name="rol_id">
                        @foreach ($Rol=App\Models\Rol::all() as $rol)
                        <option value="{{$rol->id}}">{{$rol->nombre}}</option>
                        @endforeach
                    </select>

                    <label for="">Plan</label>
                    <select class="form-control plan_id" name="plan_id">
                        @foreach (App\Models\Plan::all() as $plan)
                        <option value="{{$plan->id}}">{{$plan->nombre}}</option>
                        @endforeach
                    </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button></form>
            </div>
        </div>
    </div>
</div>



<!-- Creditos modal content -->
<div class="modal fade" id="modal_servicio" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/agregar_servicio" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="row">

                        <div class="col-md-3">
                            <label for="message-text" class="col-form-label">Nombre:</label>
                            <input class="form-control" type="text" name="nombre" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label for="message-text" class="col-form-label">Tipo:</label>
                            <select class="form-control" name="tipo" id="">
                                <option value="rent">Rent</option>
                                <option value="service">Service</option>
                                <option value="sms">SMS</option>
                                <option value="call">Call</option>
                                <option value="email">Email</option>

                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="message-text" class="col-form-label">API:</label>
                            <select class="form-control" name="api" id="">
                                <option value="apple_remove">Apple remove</option>
                                <option value="xiaomi_remove">Xiaomi remove</option>
                                <option value="apple_check">Apple check</option>
                                <option value="xiaomi_check">Xiaomi check</option>
                                <option value="email_send">Email sender</option>
                                <option value="sms_1">SMS Sender</option>

                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="message-text" class="col-form-label">Precio:</label>
                            <input class="form-control" type="text" name="precio" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="message-text" class="col-form-label">Imagen:</label>
                            <input class="form-control" type="file" name="imagen" class="form-control">
                        </div>
                        <div class="mb-3 col-md-12">
                            <label for="message-text" class="col-form-label">Descripcion:</label>
                            <textarea class="form-control" name="descripcion" id="" cols="30" rows="10"></textarea>
                        </div>


                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Regresar</button>
                <button type="submit" class="btn btn-primary">Agregar Servicio</button> </form>

            </div>
        </div>
    </div>
</div>
<!-- Creditos modal content -->
<div class="modal fade" id="modal_agregar_creditos" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Creditos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/agregar_creditos">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label for="customer-name" class="col-form-label">Nombre Cliente:</label>
                            <select name="iduser" id="iduser" class="form-control">

                                @foreach (App\Models\User::all() as $user)
                                <option value="{{$user->id}}">{{$user->name}} [ Creditos Actual: {{$user->creditos }} ]
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="message-text" class="col-form-label">Cantidad Manual:</label>
                            <input type="number" name="cantidad1" class="form-control" value="0">
                        </div>

                        <div class="mb-3 col-md-4">
                            <label for="message-text" class="col-form-label">Estatus:</label>
                            <select name="Operacion" id="Operacion" class="form-control">
                                <option value="0">No Pagado</option>
                                <option value="1">Pendiente</option>
                                <option value="2">Pagado</option>

                            </select>
                        </div>
                        <input type="hidden" name="ID_Metodo" id="ID_Metodo" value="1">

                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Regresar</button>
                <button type="submit" class="btn btn-primary">Agregar Creditos</button> </form>

            </div>
        </div>
    </div>
</div>

<!-- APi modal content -->
<div class="modal fade" id="modal_api_agregar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar API</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/agregar_api">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="mb-3 col-md-3">
                            <label for="customer-name" class="col-form-label">Nombre API:</label>
                            <input type="text" name="Nombre_API" id="Nombre_API" class="form-control">
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="customer-name" class="col-form-label">URL API:</label>
                            <input type="url" name="URL" id="URL" class="form-control">
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="message-text" class="col-form-label">Respuesta que da la API cuando es SUCCESS o
                                Afirmativa


                            </label>
                            <input type="text" name="Respuesta_OK" class="form-control">
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="message-text" class="col-form-label">Tipo de API:</label>
                            <select name="Tipo" id="Tipo" class="form-control">
                                <option value="0">Email</option>
                                <option value="1">SMS</option>
                                <option value="2">Call</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-12">
                            <label for="customer-name" class="col-form-label">Parametros de la API:<br>
                                %Mensaje esto es el texto o mensaje que mandara la api ya sea por email o sms si no
                                tiene dejalo vacio<br>
                                %Numero esto el el numero de telefono que se pondria ala api<br>
                                %Email esto el el email a que se pondria ala api<br>

                            </label>
                            <textarea class="form-control" name="Parametros" id="Parametros" cols="30" rows="10"
                                placeholder="Ejemplo: si esta es tu ruta ?mensaje=mensaje&numero=12345 <br> aqui escribirias   mensaje=%mensaje&numero=%numero "></textarea>
                        </div>

                        <div class="mb-3 col-md-12">
                            <label for="message-text" class="col-form-label">Descripcion</label>

                            <textarea class="form-control" name="descripcion" id="" cols="30" rows="10"></textarea>
                        </div>


                        <input type="hidden" name="ID_Metodo" id="ID_Metodo" value="1">

                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Regresar</button>
                <button type="submit" class="btn btn-primary">Agregar Creditos</button> </form>

            </div>
        </div>
    </div>
</div>


<!-- Tiempo modal content -->
<div class="modal fade" id="modal_tiempo" tabindex="-1" aria-labelledby="varyingcontentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Tiempo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Fecha Actual: {{\Carbon\Carbon::now()}}</p>

                <form action="/agregar_tiempo">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label for="customer-name" class="col-form-label">Nombre Cliente:</label>
                            <select name="iduser" id="iduser" class="form-control">
                                @foreach (App\Models\User::all() as $user)
                                <option value="{{$user->id}}">{{$user->name}} [ Fecha Vencimiento: {{Auth::user()->fechafinal}} ]

                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="message-text" class="col-form-label">Cantidad Manual:</label>
                            <select name="tiempo" id="tiempo" class="form-control">
                                <option value="7">7 dias</option>
                                <option value="15">15 dias</option>
                                <option value="30">30 dias</option>
                                <option value="3">3 Meses</option>
                                <option value="6">6 Meses</option>
                                <option value="1">1 Año</option>
                                <option value="2">2 Años</option>
                            </select>
                        </div>


                        <div class="mb-3 col-md-12">
                            <label for="message-text" class="col-form-label">Notas:</label>
                            <textarea name="notas" id="notas" class="form-control" cols="30" rows="10"></textarea>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Regresar</button>
                <button type="submit" class="btn btn-primary">Agregar Creditos</button> </form>

            </div>
        </div>
    </div>
</div>
