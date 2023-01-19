<?php

namespace App\Models;

use App\Traits\MultitenacyScopeFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookIssue extends Model
{
    use HasFactory, MultitenacyScopeFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'library_id',
        'user_id',
        'book_id',
        'status',
        'issue_date',
        'return_date',
        'due_date',
        'extention_num',
    ];


    public function library()
    {
        return $this->belongsTo(Library::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
