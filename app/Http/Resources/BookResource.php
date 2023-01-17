<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'id' => (string) $this->id,
            "attributes" => [
                'name' => $this->name,
                'published_year' => $this->published_year,
                'total_copies' => $this->total_copies,
                'available_copies' => $this->available_copies,
                'isbn' => $this->isbn,
                'edition' => $this->edition,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'relationships' => [
                'library_id' => (string) $this->library->id,
                'library_name' => $this->library->name,
                'library_address' => $this->library->address,
                'library_email' => $this->library->email,
                'library_phone_number' => $this->library->phone_number,
                'book_issue_duration_in_days' => $this->book_issue_duration_in_days,
                'max_issue_extentions' => $this->max_issue_extentions,
                'author_id' => (string) $this->author->id,
                'author_name' => $this->author->name,
                'publisher_id' => $this->publisher->author,
                'publisher_name' => $this->publisher->category,
                'category_id' => $this->category->publisher,
                'category_name' => $this->category->total_copies,
            ]
        ];
    }
}
