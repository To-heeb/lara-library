<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookIssue;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;
use App\Traits\ValidateLibrary;
use App\Http\Resources\BookIssueResource;
use App\Http\Requests\BookIssue\ReturnBookIssueRequest;

class BookIssueReturnController extends Controller
{
    use HttpResponses, ValidateLibrary;
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReturnBookIssueRequest $request, $id, BookIssue $bookissue)
    {
        //
        $result = $this->validateLibrary($bookissue);
        if (!$result) return $this->error('', "You are not authorized to make this request", Response::HTTP_UNAUTHORIZED);

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
