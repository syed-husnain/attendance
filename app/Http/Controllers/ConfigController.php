<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Http\Requests\StoreConfigRequest;
use App\Http\Requests\UpdateConfigRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $config = Config::first();
        return view('pages.configurations.edit',['config' => $config]);
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
     * @param  \App\Http\Requests\StoreConfigRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreConfigRequest $request)
    {
        // StoreConfigRequest
       $config = Config::updateOrCreate([
            'id' => 1
       ],[
            'start_time' => date('H:i', strtotime( $request->start_time )),
            'end_time'   => date('H:i', strtotime( $request->end_time ))
       ]);

       return response()->json([
        'status_code' => Response::HTTP_OK,
        'success'     => TRUE,
        'error'       => FALSE,
        'data'        => [],
        'message'     => 'Configuration added Successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function show(Config $config)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function edit(Config $config)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateConfigRequest  $request
     * @param  \App\Models\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateConfigRequest $request, Config $config)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function destroy(Config $config)
    {
        //
    }
}
