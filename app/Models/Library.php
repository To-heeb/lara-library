<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\Cast\Object_;

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

        $app_short_url = explode('.', config('app.short_url'));

        // echo $subdomain;
        // echo config('app.short_url');
        // exit;

        if (!$subdomain) return 0;
        if ($subdomain  == $app_short_url[0] || $subdomain  == $app_short_url[1]) return 0;

        $library = Library::where('subdomain', '=', $subdomain)->first();
        //echo $subdomain;

        return $library->id;
    }

    public static function getLibraryDetails(): ?Object
    {
        $url = request()->getHttpHost();

        $url_array = explode('.', $url);
        $subdomain = $url_array[0];

        if ($subdomain === 'www') $subdomain = $url_array[1];

        $app_short_url = explode('.', config('app.short_url'));

        if (!$subdomain) return null;
        if ($subdomain  == $app_short_url[0] || $subdomain  == $app_short_url[1]) return null;

        $library = Library::where('subdomain', 'LIKE', $subdomain)->first();

        return $library;
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
