<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\LoginUser;
use App\Http\Requests\User\StoreUser;
use App\Http\Requests\User\AuthenticateUser;

class UserController extends Controller
{

    use HttpResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return UserResource::collection(User::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, User $user)
    {
        //
        return $this->isNotAuthorized($user) ? $this->isNotAuthorized($user) : new UserResource($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
        if (Auth::user()->role == "user") {
            if (Auth::user()->id != $user->id) {
                return $this->error('', "You are not authorized to make this request", 403);
            }
        }

        $request->validated($request->all());

        User::updateUser($request->all());
        $user = User::find($user->id);

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
        if (Auth::user()->role == "admin" || Auth::user()->role == "librarian") {

            $role = ucfirst(Auth::user()->role) . 's';

            return $this->error(null, "$role are not allowed to delete account", 403);
        }
        return $this->isNotAuthorized($user) ? $this->isNotAuthorized($user) : $user->delete();
    }

    private function isNotAuthorized($user)
    {
        if (Auth::user()->role == "user") {
            if (Auth::user()->id != $user->id) {
                return $this->error('', "You are not authorized to make this request", 403);
            }
        }
    }
}
