<?php

namespace App\Http\Controllers;

use App\Author;
use Illuminate\Http\Request;
use Laratrust\LaratrustFacade;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (LaratrustFacade::hasRole('admin')) {
            return $this->adminDashboard();
        }

        if (LaratrustFacade::hasRole('member')) {
            return $this->memberDashboard();
        }

        return view('home');
    }

    public function adminDashboard()
    {
        $authors    = [];
        $books      = [];

        foreach (Author::all() as $author) {
            array_push($authors, $author->name);
            array_push($books, $author->books->count());
        }

        // dd($authors, $books);

        return view('dashboard.admin', compact('authors', 'books'));
    }

    public function memberDashboard()
    {
        $borrowLogs = auth()->user()->borrowLogs()->borrowed()->get();

        return view('dashboard.member', compact('borrowLogs'));
    }
}
