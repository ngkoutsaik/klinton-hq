<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    public function home(): View|RedirectResponse
    {
//        $user = $request->user();
//        if (!$user) {
//            $user = User::find(1);
//        }
        $superAdminId = 1;
        $resume = Resume::where(['user_id' => $superAdminId, 'published' => true])->with(
            'user',
            'activeExtraInfo',
            'skills',
            'links',
            'workExperiences'
        )->first();

        if ($resume === null) {
            return redirect('/404');
        }

        return view('home', ['resume' => $resume]);
    }
}
