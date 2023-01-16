<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    //
    use HasFactory;

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
}
