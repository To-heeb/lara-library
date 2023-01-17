<?php

namespace App\Http\Controllers;

use App\Models\BookIssue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BookIssueResource;
use App\Http\Requests\BookIssue\StoreBookIssueRequest;
use App\Http\Requests\BookIssue\UpdateBookIssueRequest;
use App\Traits\HttpResponses;

class BookIssueController extends Controller
{
    use HttpResponses;
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
    public function store(StoreBookIssueRequest $request)
    {
        //
        $book_issue_info = $request->validated($request->all());

        $book_issue = BookIssue::create($book_issue_info);

        return new BookIssueResource($book_issue);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BookIssue  $bookIssue
     * @return \Illuminate\Http\Response
     */
    public function show($id, BookIssue $bookIssue)
    {
        //
        if (Auth::user()->role == "user") {
            if (Auth::user()->id != $bookIssue->user_id) {
                return $this->error('', "You are not authorized to make this request", 403);
            }
        }
        return new BookIssueResource($bookIssue);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BookIssue  $bookIssue
     * @return \Illuminate\Http\Response
     */
    public function edit(BookIssue $bookIssue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BookIssue  $bookIssue
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBookIssueRequest $request, $id, BookIssue $bookIssue)
    {
        //
        if (Auth::user()->role == "user") {
            if (Auth::user()->id != $bookIssue->user_id) {
                return $this->error('', "You are not authorized to make this request", 403);
            }
        }

        $request->validated($request->all());

        $bookIssue->update($request->all());

        return new BookIssueResource($bookIssue);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BookIssue  $bookIssue
     * @return \Illuminate\Http\Response
     */
    public function destroy(BookIssue $bookIssue)
    {
        //
        $bookIssue->delete();

        $message = "BookIssue successfully deleted";
        return $this->success([], $message);
    }
}
