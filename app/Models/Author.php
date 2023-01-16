<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Author extends Model
{
    //
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'library_id',
        'name',
    ];


    public function library()
    {
        return $this->belongsTo(Library::class);
    }

    public function books()
    {
        return $this->hasMany(Library::class);
    }
}
