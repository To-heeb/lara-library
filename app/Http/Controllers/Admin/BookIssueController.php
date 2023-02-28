<?php

namespace App\Http\Controllers\Admin;

use App\Models\BookIssue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookIssueResource;

class BookIssueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return BookIssueResource::collection(BookIssue::all());
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(BookIssue $bookissue)
    {
        //
        return new BookIssueResource($bookissue);
    }
}
