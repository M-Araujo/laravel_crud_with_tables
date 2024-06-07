<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Services\DashboardStats;

class DashBoardController extends Controller
{
    public function index(Request $request): Factory|\Illuminate\Foundation\Application|View|Application
    {
        $stats = (new DashboardStats)->stats();


        return view('dashboard')->with(compact('stats'));
    }


}
