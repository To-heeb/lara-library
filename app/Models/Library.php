<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    //
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'subdomain',
        'email',
        'address',
        'phone_number',
        'book_issue_duration_in_days',
        'max_issue_extentions',
    ];

    public static function getLibrary(): int
    {
        $url = request()->getHttpHost();


        $url_array = explode('.', $url);
        $subdomain = $url_array[0];

        if ($subdomain === 'www') $subdomain = $url_array[1];

        if (!$subdomain) return 0;

        $library = Library::where('subdomain', 'LIKE', $subdomain)->first();
        //echo $subdomain;

        return $library->id;
    }

    public function book_issues()
    {
        return $this->hasMany(BookIssue::class);
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function publishers()
    {
        return $this->hasMany(Publisher::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
