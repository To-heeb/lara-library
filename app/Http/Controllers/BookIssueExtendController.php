<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Library;
use App\Models\BookIssue;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;
use App\Traits\ValidateLibrary;
use App\Http\Resources\BookIssueResource;
use App\Http\Requests\BookIssue\ExtendBookIssueRequest;

class BookIssueExtendController extends Controller
{
    use HttpResponses, ValidateLibrary;

    /**
     * @param ExtendBookIssueRequest $request
     * 
     * @param integer $id
     * @param BookIssue $bookissue
     * @return void
     * 
     */
    public function update(ExtendBookIssueRequest $request, $id, BookIssue $bookissue)
    {
        $result = $this->validateLibrary($bookissue);
        //dd("I got here");
        if (!$result) return $this->error('', "You are not authorized to make this request", Response::HTTP_UNAUTHORIZED);

        $book_issue_info = $request->validated();

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
