<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{

    public function index(Request $request)
    {
        $items = User::select('id', 'name', 'email', 'picture')->get();
        return view('users.list')->with(compact('items'));
    }


}
