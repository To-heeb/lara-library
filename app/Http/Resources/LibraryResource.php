<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LibraryResource extends JsonResource
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
                'subdomain' => $this->subdomain,
                'address' => $this->address,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'book_issue_duration_in_days' => $this->book_issue_duration_in_days,
                'max_issue_extentions' => $this->max_issue_extentions,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]
        ];
    }
}
