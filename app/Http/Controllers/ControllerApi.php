<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ControllerApi extends Controller
{
 
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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
