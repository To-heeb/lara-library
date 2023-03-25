<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;
use App\Traits\ValidateLibrary;
use App\Http\Resources\PublisherResource;
use App\Http\Requests\Publisher\StorePublisherRequest;
use App\Http\Requests\Publisher\UpdatePublisherRequest;

class PublisherController extends Controller
{
    use HttpResponses;



    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('validate_library');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PublisherResource::collection(Publisher::all());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePublisherRequest $request)
    {
        $publisher = Publisher::create($request->validated());

        return new PublisherResource($publisher);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function show($id, Publisher $publisher)
    {
        $this->authorize('view', $publisher);

        return new PublisherResource($publisher);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePublisherRequest $request, $id, Publisher $publisher)
    {
        $this->authorize('update', $publisher);

        $publisher->update($request->validated());

        return new PublisherResource($publisher);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Publisher $publisher)
    {
        $this->authorize('delete', $publisher);

        $publisher->delete();

        return $this->success([], "Publisher successfully deleted", Response::HTTP_NO_CONTENT);
    }
}
