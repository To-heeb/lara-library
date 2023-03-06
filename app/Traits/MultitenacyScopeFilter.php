<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Library;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;



trait MultitenacyScopeFilter
{
    use HttpResponses;

    protected static function bootMultitenacyScopeFilter()
    {

        // use this when creating the queries
        $library_id = Library::getLibrary();


        if ($library_id != 0) {

            if (auth('sanctum')->check()) {
                static::creating(function ($model) {

                    $library_id = Library::getLibrary();

                    if ($model instanceof User and $model->role == "admin") {
                        $model->library_id = 0;
                    }
                    if (auth('sanctum')->user()->role != "admin" && auth('sanctum')->user()->library_id == $library_id && !$model instanceof User) {
                        $model->library_id = $library_id;
                    }
                });

                static::updating(function ($model) {
                    //dd([Auth::user()->library_id, auth('sanctum')->user()->library_id]);
                    if (auth('sanctum')->user()->library_id != $model->library_id) {
                        return $this->error('', "You are not authorized to make this request", Response::HTTP_UNAUTHORIZED);
                    }
                });


                if (auth('sanctum')->user()->role != "admin") {

                    static::addGlobalScope(function (Builder $builder) {
                        return $builder->where('library_id', auth('sanctum')->user()->library_id);
                    });
                }
            }
        }
    }
}
