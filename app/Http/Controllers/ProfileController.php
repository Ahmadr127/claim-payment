<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function index()
    {
        $user = auth()->user();
        return view('profile.index', compact('user'));
    }

    public function toggleLayout()
    {
        $user = auth()->user();
        $user->layout_preference = $user->layout_preference === 'appbar' ? 'sidebar' : 'appbar';
        $user->save();

        return response()->json(['status' => 'success', 'layout' => $user->layout_preference]);
    }
}
