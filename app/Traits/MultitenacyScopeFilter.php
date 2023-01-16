<?php

namespace App\Traits;

use App\Models\Library;
use Illuminate\Database\Eloquent\Builder;



trait MultitenacyScopeFilter
{
    protected static function bootMultitenacyScopeFilter()
    {
        if (auth()->check()) {
            // use this when creating the queries


            $library_id = Library::getLibrary();

            //return error page here
            if ($library_id == 0) return ('welcome');

            static::creating(function ($model, $library_id) {
                $model->library_id = $library_id;
            });


            if (auth()->user()->role != "super_admin") {
                static::addGlobalScope(function (Builder $builder) {
                    return $builder->where('library_id', auth()->user()->tenant_id);
                });
            }
        }
    }
}
