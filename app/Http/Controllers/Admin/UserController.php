<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AdminResource;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return UserResource::collection(User::where('library_id', '!=', 0)->get());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
        return new AdminResource($user);
    }


    public function profile(User $user)
    {
        //
        return new UserResource($user);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        //
        if (Auth::user()->role == "admin") {
            if (Auth::user()->id != $user->id) {
                return $this->error('', "You are not authorized to make this request", 403);
            }
        }

        $request->validated($request->all());

        User::updateUser((object) $request->all());
        $user = User::find($user->id);

        return new AdminResource($user);
    }
}
