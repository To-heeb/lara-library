<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PublisherResource extends JsonResource
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
            ]

        ];
    }
}
