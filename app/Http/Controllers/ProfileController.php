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
        
        if ($user->role->name !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $newLayout = $user->layout_preference === 'appbar' ? 'sidebar' : 'appbar';
        
        // Update for all users
        \App\Models\User::query()->update(['layout_preference' => $newLayout]);

        // Refresh current user model
        $user->refresh();

        return response()->json(['status' => 'success', 'layout' => $user->layout_preference]);
    }
}
