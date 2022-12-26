<?php

namespace App\Http\Controllers;

use App\Models\UserAllowance;
use App\Http\Requests\StoreUserAllowanceRequest;
use App\Http\Requests\UpdateUserAllowanceRequest;

class UserAllowanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreUserAllowanceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserAllowanceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserAllowance  $userAllowance
     * @return \Illuminate\Http\Response
     */
    public function show(UserAllowance $userAllowance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserAllowance  $userAllowance
     * @return \Illuminate\Http\Response
     */
    public function edit(UserAllowance $userAllowance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserAllowanceRequest  $request
     * @param  \App\Models\UserAllowance  $userAllowance
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserAllowanceRequest $request, UserAllowance $userAllowance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserAllowance  $userAllowance
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserAllowance $userAllowance)
    {
        //
    }
}
