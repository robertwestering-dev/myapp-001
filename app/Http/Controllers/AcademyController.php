<?php

namespace App\Http\Controllers;

use App\Models\AcademyCourse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademyController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('academy.index', [
            'courses' => AcademyCourse::query()
                ->active()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
            'user' => $user,
        ]);
    }
}
