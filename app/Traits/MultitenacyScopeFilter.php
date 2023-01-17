<?php

namespace App\Traits;

use App\Models\Library;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;



trait MultitenacyScopeFilter
{
    protected static function bootMultitenacyScopeFilter()
    {


        // use this when creating the queries
        $library_id = Library::getLibrary();

        if ($library_id != 0) {

            if (auth()->check()) {

                static::creating(function ($model, $library_id) {

                    if ($model instanceof User and $model->role == "admin") {
                        $model->library_id = 0;
                    } else {
                        $model->library_id = $library_id;
                    }
                });


                if (auth()->user()->role != "admin") {
                    static::addGlobalScope(function (Builder $builder) {
                        return $builder->where('library_id', auth()->user()->library_id);
                    });
                }
            }
        }



        // if (auth()->check()) {
        //     // use this when creating the queries


        //     $library_id = Library::getLibrary();

        //     //return error page here
        //     if ($library_id == 0) return ('welcome');

        //     static::creating(function ($model, $library_id) {

        //         if ($model instanceof User and $model->role == "admin") {
        //             $model->library_id = 0;
        //         } else {
        //             $model->library_id = $library_id;
        //         }
        //     });


        //     if (auth()->user()->role != "admin") {
        //         static::addGlobalScope(function (Builder $builder) {
        //             return $builder->where('library_id', auth()->user()->library_id);
        //         });
        //     }
        // }
    }
}
