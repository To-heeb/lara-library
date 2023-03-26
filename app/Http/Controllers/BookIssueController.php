<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Library;
use App\Models\BookIssue;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BookIssueResource::collection(BookIssue::all());
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
     * @id
     * @param  \App\Models\BookIssue  $bookissue
     * @return \Illuminate\Http\Response
     */
    public function show($id, BookIssue $bookissue)
    {
        $this->authorize('view', $bookissue);

        return new BookIssueResource($bookissue);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UpdateBookIssueRequest  $request
     * @param  \App\Models\BookIssue  $bookIssue
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBookIssueRequest $request, $id, BookIssue $bookissue)
    {
        $this->authorize('update', $bookissue);

        $bookissue->update($request->validated());

        return new BookIssueResource($bookissue);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @id
     * @param  \App\Models\BookIssue  $bookIssue
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, BookIssue $bookissue)
    {
        $this->authorize('delete', $bookissue);

        $bookissue->delete();

        return $this->success([], "BookIssue successfully deleted", Response::HTTP_NO_CONTENT);
    }


    /**
     * @param array $book_issue_info
     * 
     * @return BookIssue 
     */
    private function validateBook($book_issue_info): ?Object
    {

        $pending_book = BookIssue::where([
            ["status", "=", 'pending'],
            ["user_id", "=", $book_issue_info['user_id']],
            ["book_id", "=", $book_issue_info['book_id']]
        ])->first();

        return $pending_book;
    }
}
