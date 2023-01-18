<?php

namespace App\Http\Controllers;

use App\Models\Library;
use App\Models\BookIssue;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BookIssueResource;
use App\Http\Requests\BookIssue\StoreBookIssueRequest;
use App\Http\Requests\BookIssue\UpdateBookIssueRequest;

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

        // issue date 
        $book_issue_info['issue_date'] = date('Y-m-d');

        // due date
        $library_info = Library::getLibraryDetails();
        $addedDays = intval($library_info->book_issue_duration_in_days);
        $due_date =  date('Y-m-d', strtotime($book_issue_info['issue_date'] . " +  $addedDays days"));
        $book_issue_info['due_date'] = $due_date;

        $book_issue = BookIssue::create($book_issue_info);

        return new BookIssueResource($book_issue);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BookIssue  $bookissue
     * @return \Illuminate\Http\Response
     */
    public function show(BookIssue $bookissue)
    {
        //
        if (Auth::user()->role == "user") {
            if (Auth::user()->id != $bookissue->user_id) {
                return $this->error('', "You are not authorized to make this request", 403);
            }
        }

        echo '<pre>';
        var_dump($bookissue);
        echo '</pre>';
        exit;
        return new BookIssueResource($bookissue);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BookIssue  $bookIssue
     * @return \Illuminate\Http\Response
     */
    public function edit(BookIssue $bookissue)
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
    public function update(UpdateBookIssueRequest $request, $id, BookIssue $bookissue)
    {
        //
        if (Auth::user()->role == "user") {
            if (Auth::user()->id != $bookissue->user_id) {
                return $this->error('', "You are not authorized to make this request", 403);
            }
        }

        $request->validated($request->all());

        $bookissue->update($request->all());

        return new BookIssueResource($bookissue);
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
