<?php

namespace App\Models;

use App\Traits\MultitenacyScopeFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Publisher extends Model
{
    use HasFactory, MultitenacyScopeFilter;

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
        return $this->hasMany(Book::class);
    }
}
