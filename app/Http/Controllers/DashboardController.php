<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        return Inertia::render('Dashboard', [
            'user' => $request->user(),
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ],
        ]);
    }
}
