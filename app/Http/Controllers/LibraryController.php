<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Library;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\LibraryResource;
use App\Http\Requests\Library\StoreLibraryRequest;
use App\Http\Requests\Library\UpdateLibraryRequest;
use App\Traits\HttpResponses;

class LibraryController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return LibraryResource::collection(Library::all());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLibraryRequest $request)
    {
        $library = Library::create($request->validated());

        if (Auth::user()->library_id == 0) {

            $library_id = $library->id;
            User::updateLibraryIDForLibrarian($library_id);
        }

        return new LibraryResource($library);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Library  $library
     * @return \Illuminate\Http\Response
     */
    public function show($id, Library $library)
    {
        return new LibraryResource($library);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Library  $library
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLibraryRequest $request, $id, Library $library)
    {
        $this->authorize('update', $library);

        $library->update($request->validated());

        return new LibraryResource($library);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Library  $library
     * @return \Illuminate\Http\Response
     */
    public function destroy(Library $library)
    {
        //
    }
}
