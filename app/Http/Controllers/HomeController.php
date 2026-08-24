<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request)
    {
//        $user = $request->user();
//        if (!$user) {
//            $user = User::find(1);
//        }
        $superAdminId = 1;
        $resume = Resume::where('user_id', $superAdminId)->with(
            'user',
            'skills',
            'links',
            'workExperiences'
        )->first();

        return view('home', ['resume' => $resume]);
    }
}
