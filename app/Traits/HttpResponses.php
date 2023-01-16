<?php

namespace App\Traits;


trait HttpResponses
{

    protected function success($data, $message = null, $status_code = 200)
    {

        return response()->json([
            'status' => 'Request was successful',
            'message' => $message,
            'data' => $data
        ], $status_code);
    }

    protected function error($data, $message = null, $status_code)
    {

        return response()->json([
            'status' => 'Error has occured',
            'message' => $message,
            'data' => $data
        ], $status_code);
    }
}
