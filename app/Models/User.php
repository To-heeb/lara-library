<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use App\Traits\MultitenacyScopeFilter;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, MultitenacyScopeFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'library_id',
        'name',
        'first_name',
        'last_name',
        'role',
        'email',
        'phone_number',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function updateLibraryIDForLibrarian($library_id)
    {
        $librarian = User::find(Auth::id());
        $librarian->library_id  = $library_id;


        $librarian->save();
    }

    public static function updateLibraryIDForUser($library_id)
    {
        $user = User::find(Auth::id());
        $user->library_id  = $library_id;


        $user->save();
    }

    public function library()
    {
        return $this->belongsTo(Library::class);
    }

    public function book_issues()
    {
        return $this->hasMany(BookIssue::class);
    }
}
