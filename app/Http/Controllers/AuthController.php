<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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

    public function issueToken(Request $request) {
        $validatedData = $request->validate([
            'email' => 'required|email|string',
            'password'=> 'required|string'
        ]);

        if (Auth::attempt($validatedData)) {
            $user = Auth::user();

            $token = $user->createToken('api_token');

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token->plainTextToken
            ], 200);
            
        } else {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
    }
}
