<?php

namespace App\Http\Controllers\Admin;

use App\Models\Library;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LibraryResource;

class LibraryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return LibraryResource::collection(Library::all());
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Library $library)
    {
        //
        return new LibraryResource($library);
    }
}
