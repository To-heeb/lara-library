<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Library;
use App\Models\BookIssue;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BookIssueResource;
use App\Http\Requests\BookIssue\StoreBookIssueRequest;
use App\Http\Requests\BookIssue\ExtendBookIssueRequest;
use App\Http\Requests\BookIssue\ReturnBookIssueRequest;
use App\Http\Requests\BookIssue\UpdateBookIssueRequest;

class BookIssueController extends Controller
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
        $book_issue_info = $request->validated($request->all());

        //check if the book is available
        $book = Book::find($book_issue_info['book_id']);
        if ($book->available_copies <= 0) return $this->success([], "Book is not currently available", 204);

        //check if user doesn't have pending issue with the same book
        $pending_book = $this->validateBook($book_issue_info);

        if ($pending_book) return $this->error([], "You have a pending issue on this book, the same book can't be issue twice", 401);

        // issue date 
        $book_issue_info['issue_date'] = date('Y-m-d');

        // due date
        $library_info = Library::getLibraryDetails();
        $addedDays = intval($library_info->book_issue_duration_in_days);
        $due_date =  date('Y-m-d', strtotime($book_issue_info['issue_date'] . " +  $addedDays days"));
        $book_issue_info['due_date'] = $due_date;

        $book_issue = BookIssue::create($book_issue_info);

        Book::updateBookCopies($book_issue_info['book_id'], "decrease");

        return new BookIssueResource($book_issue);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BookIssue  $bookissue
     * @return \Illuminate\Http\Response
     */
    public function show($id, BookIssue $bookissue)
    {
        //
        if (Auth::user()->role == "user") {
            if (Auth::user()->id != $bookissue->user_id) {
                return $this->error('', "You are not authorized to make this request", 403);
            }
        }

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

        //
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


    /**
     * @param ReturnBookIssueRequest $request
     * 
     * @param integer $id
     * @param BookIssue $bookissue
     * @return void
     * 
     */
    public function returnBook(ReturnBookIssueRequest $request, $id, BookIssue $bookissue)
    {

        $book_issue_info = $request->validated($request->all());
        $pending_book = $this->validateBook($book_issue_info);

        if (!$pending_book) return $this->error([], "You don't have a pending issue on this book, you can't return an issue on it", 401);

        $book_issue_info["status"] = 'returned';
        $bookissue->update($book_issue_info);

        Book::updateBookCopies($book_issue_info['book_id'], "increase");

        $book_resource = new BookIssueResource($bookissue);
        return $this->success($book_resource, "Book issue successfully returned, thank you.");
    }



    /**
     * @param ExtendBookIssueRequest $request
     * 
     * @param integer $id
     * @param BookIssue $bookissue
     * @return void
     * 
     */
    public function extendBook(ExtendBookIssueRequest $request, $id, BookIssue $bookissue)
    {

        $book_issue_info = $request->validated($request->all());

        //check if user have pending issue with the same book
        $pending_book = $this->validateBook($book_issue_info);

        if (!$pending_book) return $this->error([], "You don't have a pending issue on this book, you can't extend an issue on it", 401);

        $library_info = Library::getLibraryDetails();

        // check for max extention
        if ($pending_book["extention_num"] >=  $library_info->max_issue_extentions) return $this->error([], "You have exceeded the number of extentions allowed", 401);

        $addedDays = intval($library_info->book_issue_duration_in_days);
        $due_date =  date('Y-m-d', strtotime($pending_book->due_date . " +  $addedDays days"));
        $book_issue_info["due_date"] = $due_date;
        $book_issue_info["extention_num"] = $pending_book["extention_num"] + 1;
        $bookissue->update($book_issue_info);

        $book_resource = new BookIssueResource($bookissue);
        return $this->success($book_resource, "Book issue successfully extended.");
    }


    /**
     * @param array $book_issue_info
     * 
     * @return BookIssue 
     */
    public function validateBook($book_issue_info): ?Object
    {

        $pending_book = BookIssue::where([
            ["status", "=", 'pending'],
            ["user_id", "=", $book_issue_info['user_id']],
            ["book_id", "=", $book_issue_info['book_id']]
        ])->first();

        return $pending_book;
    }
}
