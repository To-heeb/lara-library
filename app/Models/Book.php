<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'library_id',
        'publisher_id',
        'category_id',
        'author_id',
        'available_copies',
        'total_copies',
        'published_year',
        'isbn',
        'edition',
    ];



    public function library()
    {
        return $this->belongsTo(Library::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function book_issues()
    {
        return $this->hasMany(User::class);
    }
}
