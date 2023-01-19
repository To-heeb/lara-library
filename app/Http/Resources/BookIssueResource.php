<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookIssueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' =>  $this->id,
            "attributes" => [
                'issue_date' => $this->issue_date,
                'return_date' => $this->return_date,
                'due_date' => $this->due_date,
                'status' => $this->status,
                'extention_num' => $this->extention_num,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'relationships' => [
                'library_id' => (string) $this->library->id,
                'library_name' => $this->library->name,
                'library_address' => $this->library->address,
                'library_email' => $this->library->email,
                'library_phone_number' => $this->library->phone_number,
                'book_issue_duration_in_days' => $this->library->book_issue_duration_in_days,
                'max_issue_extentions' => $this->library->max_issue_extentions,
                'book_id' => (string) $this->book->id,
                'book_name' => $this->book->name,
                'book_author' => $this->book->author,
                'book_category' => $this->book->category,
                'book_publisher' => $this->book->publisher,
                'total_copies' => $this->book->total_copies,
                'available_copies' => $this->book->available_copies,
                'published_year' => $this->book->published_year,
                'isbn' => $this->book->isbn,
                'edition' => $this->book->edition,

            ]

        ];
    }
}
