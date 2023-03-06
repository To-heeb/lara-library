<?php

namespace App\Models;

use App\Traits\MultitenacyScopeFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{

    use HasFactory, MultitenacyScopeFilter;

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

    /**
     * Update the books available after an issue havae been created.
     * 
     * @param integer $book_id
     * @param string $status
     * @return void
     */
    public static function updateBookCopies($book_id, $status)
    {
        $book = Book::find($book_id);

        if ($status == 'decrease') {

            $book->available_copies = $book->available_copies - 1;
        } elseif ($status == 'increase') {

            $book->available_copies = $book->available_copies + 1;
        }

        $book->save();
    }

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
