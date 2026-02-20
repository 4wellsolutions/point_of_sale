<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function loginUserById()
    {
        // Find the user by ID (user with ID 1)
        $user = User::find(1);

        if ($user) {
            // Log in the user
            Auth::login($user);
            
            // Optionally, you can redirect the user to a specific page after login
            return redirect()->route('home'); // or wherever you want to redirect the user
        } else {
            // Handle case when user is not found
            return redirect()->route('login')->with('error', 'User not found');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
