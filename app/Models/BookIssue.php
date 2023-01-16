<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookIssue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'library_id',
        'user_id',
        'book_id',
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

    public function book_issues()
    {
        return $this->hasMany(User::class);
    }
}
