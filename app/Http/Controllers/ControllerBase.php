<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Credito;
use App\Models\Servicio;
use App\Models\Setting;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;
use Carbon\Carbon;

class ControllerBase extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     * int id
     */
    public function editar_usuario(Request $request)
    {
        try {
            $usuario =  User::FindOrFail($request->id_usuario);
            $usuario->name = $request->name;
            $usuario->email = $request->email;
            $usuario->rol_id = $request->rol_id;
            $usuario->plan_id = $request->plan_id;
            if ($request->password != '') {
                $contrasena = $request->password;
                $usuario->password = bcrypt($contrasena);
            }
            //  Mail::to("$request->email")->send(new Notificacion_Usuario($usuario, $contrasena));
            // dd($contrasena);
            $usuario->save();
            return back();
        } catch (Exception $e) {
            return back();
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function agregar_usuario(Request $request)
    {
        try {
            $usuario = new User();
            $usuario->name = $request->name;
            $usuario->email = $request->email;
            $usuario->rol_id = $request->rol_id;
            $usuario->plan_id = $request->plan_id;
            $usuario->fechaactivo = Carbon::now();
            $usuario->fechafinal = '2022-06-30 00:00:00';
            $contrasena = $this->generateRandomString();
            $usuario->password = bcrypt($contrasena);
            $usuario->estatus = 1;
            $usuario->creditos = 0;
            $usuario->profile_photo_path = "https://cdn-icons-png.flaticon.com/512/6073/6073873.png";
            //  Mail::to("$request->email")->send(new Notificacion_Usuario($usuario, $contrasena));
            // dd($contrasena);
            $usuario->save();
            return back();
        } catch (Exception $e) {
            dd($e);
        }
    }

   /**
     * Store a newly created resource in storage.
     */
    public function agregar_servicio(Request $request)
    {
        try {

               // Validar el formulario (tamaño, tipo de archivo, etc.)
      /*  $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);*/

        // Subir la imagen al almacenamiento
        $imagenPath = $request->file('imagen')->store('/imagenes');

            $user = Servicio::create([
                'nombre' => $request->nombre,
                'tipo' => $request->tipo,
                'api' => $request->api,
                'precio' => $request->precio,
                'descripcion' => $request->descripcion,
                'imagen' => $imagenPath,
                'estatus' => '1',

            ]);
            return back()->with('success', 'Imagen subida correctamente.')
            ->with('imagen', $imagenPath);;
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function busca_rol_nombre(string $id)
    {
        $nombre = User::FindOrFail($id);
        return $nombre;
        //{{app('\App\Http\Controllers\ControllerMuestra')->busca_rol_nombre($credito->ID_Autorizo)}}
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function agregar_creditos(Request $request)
    {
        //  $balance = DB::table('creditos')->where('iduser', $request->iduser)->sum('creditos');
        $balanceactual = User::FindOrFail($request->iduser)->creditos;
        $temp = $balanceactual + $request->cantidad1;

        $usuario = User::FindOrFail($request->iduser);
        $usuario->creditos = $temp;
        $usuario->save();

        $creditos = new Credito;
        $creditos->Cantidad = $request->cantidad1;
        $creditos->ID_Autorizo = Auth::User()->id;
        $creditos->ID_Usuario = $request->iduser;
        $creditos->ID_Metodo = $request->ID_Metodo;
        $creditos->Operacion = $request->Operacion;

        $creditos->save();

        //  $this->notifica($request->iduser, 1);
        return back()->with('Success', "" . $creditos->creditos . " Creditos agregados con exito");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $iduser
     * @param  int  $idcreditos
     * @return \Illuminate\Http\Response
     */
    public function creditos($id)
    {
        //$balance = DB::table('creditos')->where('iduser',$request->iduser)->sum('creditos');
        $busqueda = Credito::find($id);
        $creditos = new Credito;
        $creditos->creditos = $busqueda->creditos;
        $creditos->admin = Auth::user()->email;
        $creditos->iduser = $busqueda->iduser;
        $creditos->notas = $busqueda->notas;
        $creditos->fechacarga = Carbon::now();
        $creditos->estatus = $busqueda->estatus;
        $creditos->save();
        // $this->notifica($busqueda->iduser, 1);
        return back();
    }
    public function notifica($mensaje, $envia)
    {

        $settings = Setting::find(1);
        try {
            $usuario = User::find($mensaje->iduser);
            $creditos = $mensaje->creditos;
            $agrega_creditos = 'Estimado : ' . $usuario->name . ' se a agregado satisfactoriamente ' . $creditos . ' creditos su cuenta su saldo actual es ' . app('App\Http\Controllers\ControllerBase')->obtener_creditos($usuario->id) . ' creditos  ID: ' . $mensaje->id . ' atte: ' . config('app.name', 'Laravel') . ' ';
            $recuerda_creditos = 'Estimado : ' . $usuario->name . ' se le invita a pagar el pendiente de ' . $creditos . ' creditos a la brevedad posible ID: ' . $mensaje->id . ' atte: ' . config('app.name', 'Laravel') . ' ';
        } catch (\Exception $e) {
            $usuario = User::find($mensaje);
            $creditos =  app('App\Http\Controllers\ControllerBase')->obtener_creditos($usuario->id);
            $agrega_creditos = 'Estimado : ' . $usuario->name . ' se a agregado satisfactoriamente ' . $creditos . ' creditos su cuenta su saldo actual es ' . app('App\Http\Controllers\ControllerBase')->obtener_creditos($usuario->id) . ' creditos  atte: ' . config('app.name', 'Laravel') . ' ';
            $creditos = app('App\Http\Controllers\ControllerBase')->obtener_creditos($usuario->id);
            $sin_creditos = 'Estimado : ' . $usuario->name . ' se ha quedado sin creditos si desea seguir usando nuestros servicios recargue a la brevedad posible  atte: ' . config('app.name', 'Laravel') . ' ';
            $vencimiento = 'Estimado : ' . $usuario->name . ' se ha quedado sin tiempo si desea seguir usando nuestros servicios recargue a la brevedad posible  atte: ' . config('app.name', 'Laravel') . ' ';
            $passcode = 'Codigo Obtenido: ' . $mensaje;
        }


        $settings = Setting::find(1);
        switch ($envia) {
                //creditos
            case 1:
                $message = $agrega_creditos;
                break;
            case 2:
                $message = $recuerda_creditos;
                break;
            case 3:
                $message = $sin_creditos;
                break;
            case 4:
                break;
            case 5:
                $message = $vencimiento;
                break;
            case 6:
                $message = $passcode;
                break;
        }

        if ($settings->habilita_telegram == 'on') {
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, "https://api.telegram.org/" . $settings->setting_notificaciones_telegram_bot . "/sendMessage");
            curl_setopt($c, CURLOPT_TIMEOUT, 30);
            curl_setopt($c, CURLOPT_POST, 1);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            $postfields = 'chat_id=' . Auth::user()->chatid . '&text=' . $message . '&parse_mode=html';
            curl_setopt($c, CURLOPT_POSTFIELDS, $postfields);
            $response = curl_exec($c);
            curl_close($c);
        }
        if ($settings->habilita_sms == 'on') {
            $servicio = 90;
            $senderid = "info";
            switch ($settings->setting_notificaciones_sms_seleccionado) {
                case "ghost":
                    $result =   app('\App\Http\Controllers\ControllerSMS')->ghost($usuario->phone, $message);

                    break;
                case "real":
                    $result =   app('\App\Http\Controllers\ControllerSMS')->realsms($usuario->phone, $message);

                    break;
                case "senderworld":
                    $result =   app('\App\Http\Controllers\ControllerSMS')->senderworld($usuario->phone, $message, $senderid, $servicio);


                    break;
                case "senderworldv2":
                    $result =   app('\App\Http\Controllers\ControllerSMS')->senderworldv2($usuario->phone, $message, $senderid);

                    break;
            }
        }
        if ($settings->habilita_whatsapp == 'on') {
            //whatsapp($number, $message)
            $result =   app('\App\Http\Controllers\ControllerBase')->whatsapp($usuario->phone, $message);
        }
        if ($settings->habilita_email == 'on') {
        }
    }


      /**
     * Remove the specified resource from storage.
     *
     * @param  int  $iduser
     * @param  int  $idcreditos
     * @return \Illuminate\Http\Response
     */
    public function recuerda_pago($idcreditos)
    {
        $busqueda = Credito::find($idcreditos);
        $this->notifica($busqueda, 2);
        return back();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $iduser
     * @param  int  $idcreditos
     * @return \Illuminate\Http\Response
     */
    public function reciclar_creditos($idcreditos)
    {
        //$balance = DB::table('creditos')->where('iduser',$request->iduser)->sum('creditos');
        $busqueda = Credito::find($idcreditos);
        $creditos = new Credito;
        $creditos->Cantidad = $busqueda->Cantidad;
        $creditos->ID_Autorizo = Auth::user()->id;
        $creditos->ID_Usuario = $busqueda->iduser;
        $creditos->Operacion = $busqueda->Operacion;
        $creditos->save();
        $usuario = User::find($busqueda->ID_Usuario);
        $anterior = $usuario->creditos;
        $usuario->Creditos =  $busqueda->Cantidad + $anterior;
        $usuario->save();
        //$this->notifica($busqueda->iduser, 1);
        return back();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_creditos($id)
    {
        $user = User::find($id);
        return view('admin/creditos/show')->with('id', $id)->with('user', $user);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function eliminar_usuario(string $id)
    {
        $eliminar = User::FindOrFail($id);
        $eliminar->estatus = 0;
        $eliminar->save();
        return back();
        //
    }
}
