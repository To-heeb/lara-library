<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\LoginUser;
use App\Http\Requests\User\StoreUser;
use App\Http\Requests\User\AuthenticateUser;

class AuthController extends Controller
{

    use HttpResponses;

    // LOGIN

    // user_login 
    public function user_login(LoginUser $request)
    {
        $userInfo = $request->validated($request->all());
        $userInfo['role'] = "user";

        if (!auth()->attempt($userInfo)) {
            return $this->error('', 'Credentials do no match', 401);
        }

        $user = User::where([
            'email' => $userInfo['email'],
            'role' => $userInfo['role'],
        ])->first();

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token of ' . $user->name)->plainTextToken,
        ]);
    }

    // librarian_login 
    public function librarian_login(LoginUser $request)
    {
        $librarianInfo = $request->validated($request->all());
        $librarianInfo['role'] = "librarian";

        if (!auth()->attempt($librarianInfo)) {
            return $this->error('', 'Credentials do no match', 401);
        }

        $librarian = User::where([
            'email' => $librarianInfo['email'],
            'role' => $librarianInfo['role'],
        ])->first();

        return $this->success([
            'user' => $librarian,
            'token' => $librarian->createToken('API Token of ' . $librarian->name)->plainTextToken,
        ]);
    }

    // admin_login 
    public function admin_login(LoginUser $request)
    {
        $adminInfo = $request->validated($request->all());
        $adminInfo['role'] = "admin";

        if (auth('api')->attempt($adminInfo)) {
            return $this->success([]);
        }

        $admin = User::where([
            'email' => $adminInfo['email'],
            'role' => $adminInfo['role'],
        ])->first();

        return $this->success([
            'user' => $admin,
            'token' => $admin->createToken('API Token of ' . $admin->name)->plainTextToken,
        ]);
    }

    // REGISTRATION
    // user_regsiter 
    public function user_register(StoreUser  $request)
    {
        $userInfo = $request->validated($request->all());
        $userInfo['password'] =  Hash::make($userInfo['password']);
        $userInfo['role'] = "user";
        $userInfo['name'] = $userInfo['user_name'];

        $user = User::create($userInfo);

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token of ' . $user->name)->plainTextToken,
        ]);
    }

    public function librarian_register(StoreUser  $request)
    {
        $librarianInfo = $request->validate($request->all());
        $librarianInfo['password'] =  Hash::make($librarianInfo['password']);
        $librarianInfo['role'] = "user";
        $librarianInfo['name'] = $librarianInfo['user_name'];

        $librarian = User::create($librarianInfo);

        return $this->success([
            'user' => $librarian,
            'token' => $librarian->createToken('API Token of ' . $librarian->name)->plainTextToken,
        ]);
    }

    public function admin_register(StoreUser  $request)
    {
        $adminInfo = $request->validate($request->all());
        $adminInfo['password'] =  Hash::make($adminInfo['password']);
        $adminInfo['role'] = "user";
        $adminInfo['name'] = $adminInfo['user_name'];

        $admin = User::create($adminInfo);

        return $this->success([
            'user' => $admin,
            'token' => $admin->createToken('API Token of ' . $admin->name)->plainTextToken,
        ]);
    }


    public function logout()
    {
    }
}
