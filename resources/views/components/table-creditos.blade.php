<table id="scroll-vertical"
class="table align-middle table-bordered dt-responsive nowrap mdl-data-table"
style="width:100%">
<thead>
    <tr>
        <th>Opciones</th>
        <th>Fecha</th>
        <th>Cantidad</th>
        <th>Estatus</th>
        <th>Autorizo</th>
    </tr>
</thead>
<tbody>

    @foreach(App\Models\Credito::all()->sortBy('id') as $credito)
    <tr>
        <th>
            <div class="btn-group">

                <a href="/reciclar_creditos/{{$credito->id}}" type="button"
                    class="btn btn-outline-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="currentColor" class="bi bi-recycle" viewBox="0 0 16 16">
                        <path
                            d="M9.302 1.256a1.5 1.5 0 0 0-2.604 0l-1.704 2.98a.5.5 0 0 0 .869.497l1.703-2.981a.5.5 0 0 1 .868 0l2.54 4.444-1.256-.337a.5.5 0 1 0-.26.966l2.415.647a.5.5 0 0 0 .613-.353l.647-2.415a.5.5 0 1 0-.966-.259l-.333 1.242-2.532-4.431zM2.973 7.773l-1.255.337a.5.5 0 1 1-.26-.966l2.416-.647a.5.5 0 0 1 .612.353l.647 2.415a.5.5 0 0 1-.966.259l-.333-1.242-2.545 4.454a.5.5 0 0 0 .434.748H5a.5.5 0 0 1 0 1H1.723A1.5 1.5 0 0 1 .421 12.24l2.552-4.467zm10.89 1.463a.5.5 0 1 0-.868.496l1.716 3.004a.5.5 0 0 1-.434.748h-5.57l.647-.646a.5.5 0 1 0-.708-.707l-1.5 1.5a.498.498 0 0 0 0 .707l1.5 1.5a.5.5 0 1 0 .708-.707l-.647-.647h5.57a1.5 1.5 0 0 0 1.302-2.244l-1.716-3.004z" />
                    </svg></a>
                <a href="/recuerda_pago/{{$credito->id}}" type="button"
                    class="btn btn-outline-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="currentColor" class="bi bi-bell-fill" viewBox="0 0 16 16">
                        <path
                            d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zm.995-14.901a1 1 0 1 0-1.99 0A5.002 5.002 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901z" />
                    </svg></a>

            </div>
        </th>
        <th>{{$credito->created_at}}</th>
        <th>{{$credito->Cantidad}}</th>
        <th>@if($credito->Operacion==2)

            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                fill="currentColor" class="bi bi-check-square-fill text-success"
                viewBox="0 0 16 16">
                <path
                    d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z" />
            </svg>
            @elseif($credito->Operacion==1)
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                fill="currentColor" class="bi bi-exclamation-square-fill text-warning"
                viewBox="0 0 16 16">
                <path
                    d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm6 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
            </svg>
            @elseif($credito->Operacion==0)
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                fill="currentColor" class="bi bi-exclamation-octagon-fill text-danger"
                viewBox="0 0 16 16">
                <path
                    d="M11.46.146A.5.5 0 0 0 11.107 0H4.893a.5.5 0 0 0-.353.146L.146 4.54A.5.5 0 0 0 0 4.893v6.214a.5.5 0 0 0 .146.353l4.394 4.394a.5.5 0 0 0 .353.146h6.214a.5.5 0 0 0 .353-.146l4.394-4.394a.5.5 0 0 0 .146-.353V4.893a.5.5 0 0 0-.146-.353L11.46.146zM8 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
            </svg>
            @endif
        </th>
        <th>{{$credito->ID_Autorizo}}
        </th>

    </tr>
    @endforeach
</tbody>

</table>
