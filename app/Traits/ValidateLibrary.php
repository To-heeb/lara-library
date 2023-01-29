<?php

namespace App\Traits;

use App\Models\Library;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;


trait ValidateLibrary
{

    protected function validateLibrary(Model $model)
    {
        //dd([$model->library_id, Auth::user()->library_id]);
        if ($model->library_id != Auth::user()->library_id) {
            return false;
        }
        return true;
    }
}
