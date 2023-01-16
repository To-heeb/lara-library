<?php

namespace App\Http\Controllers;

use App\Traits\HttpResponses;
use Illuminate\Http\Request;

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
    public function show($id)
    {
        //
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
    public function update(Request $request, $id)
    {
        //
    }

    // user_login 
    public function user_login(Request $request)
    {
        $userInfo = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);

        $userInfo['role'] = "user";
        if (auth('api')->attempt($userInfo)) {
            $request->session()->regenerateToken();
        }

        //return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
    }

    // librarian_login 
    public function librarian_login(Request $request)
    {
        $librarianInfo = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);

        $librarianInfo['role'] = "librarian";
        if (auth('api')->attempt($librarianInfo)) {
            $request->session()->regenerateToken();
        }

        //return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
    }

    // admin_login 
    public function admin_login(Request $request)
    {
        $adminInfo = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);

        $adminInfo['role'] = "admin";
        if (auth('api')->attempt($adminInfo)) {
            $request->session()->regenerateToken();
        }

        //return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
    }


    public function logout()
    {
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
