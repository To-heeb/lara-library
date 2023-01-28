<?php

namespace App\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;


trait ValidateLibrary
{


    protected function validateLibrary(Model $model)
    {
        //d([$model->library_id, Auth::user()->library_id]);
        if ($model->library_id != Auth::user()->library_id) {
            return false;
        }
        return true;
    }
}
