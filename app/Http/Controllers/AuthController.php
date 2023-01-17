<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Library;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\AuthenticateUser;
use App\Http\Requests\User\LoginUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    use HttpResponses;

    // LOGIN

    // user_login 
    public function user_login(LoginUserRequest $request)
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

        // update library id
        if ($user->library_id == 0) {

            $library_id = Library::getLibrary();
            User::updateLibraryIDForUser($library_id);
        }

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token of ' . $user->name)->plainTextToken,
        ]);
    }

    // librarian_login 
    public function librarian_login(LoginUserRequest $request)
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

        // update library id
        if ($librarian->library_id == 0) {

            $library_id = Library::getLibrary();
            User::updateLibraryIDForLibrarian($library_id);
        }

        return $this->success([
            'user' => $librarian,
            'token' => $librarian->createToken('API Token of ' . $librarian->name)->plainTextToken,
        ]);
    }

    // admin_login 
    public function admin_login(LoginUserRequest $request)
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
    public function user_register(StoreUserRequest  $request)
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

    public function librarian_register(StoreUserRequest  $request)
    {
        $librarianInfo = $request->validated($request->all());
        $librarianInfo['password'] =  Hash::make($librarianInfo['password']);
        $librarianInfo['role'] = "librarian";
        $librarianInfo['name'] = $librarianInfo['user_name'];

        $librarian = User::create($librarianInfo);

        return $this->success([
            'user' => $librarian,
            'token' => $librarian->createToken('API Token of ' . $librarian->name)->plainTextToken,
        ]);
    }

    public function admin_register(StoreUserRequest  $request)
    {
        $adminInfo = $request->validated($request->all());
        $adminInfo['password'] =  Hash::make($adminInfo['password']);
        $adminInfo['role'] = "admin";
        $adminInfo['name'] = $adminInfo['user_name'];

        $admin = User::create($adminInfo);

        return $this->success([
            'user' => $admin,
            'token' => $admin->createToken('API Token of ' . $admin->name)->plainTextToken,
        ]);
    }


    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success([
            'message' => "You have been logged out and no longer have access token"
        ]);
    }
}
