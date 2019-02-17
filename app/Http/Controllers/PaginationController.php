<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class PaginationController extends Controller
{
    public function index()
    {
        // $users = User::paginate(10)->linksOnEachSide(5);
        // //dd($usersPag);
        // return view('import', compact('users'));
    }
}
