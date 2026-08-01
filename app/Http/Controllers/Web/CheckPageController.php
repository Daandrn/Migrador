<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Check;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckPageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {  
        return Inertia::render(component: 'Check/Check');
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
    public function show(Check $check)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Check $check)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Check $check)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Check $check)
    {
        //
    }
}
