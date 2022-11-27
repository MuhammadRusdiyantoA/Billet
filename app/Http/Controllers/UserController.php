<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::all();
        if ($user->isEmpty()) {
            return response()->json([
                'status' => 200,
                'message' => 'Current table is empty.'
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'message' => 'Data fetched successfully.',
                'data' => $user
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'string|max:255|unique:users,name',
            'email' => 'email|max:255|unique:users,email',
            'password' => 'string|max:255',
            'balance' => 'numeric|nullable',
            'role' => 'numeric'
        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'balance' => $request->balance,
            'role' => $request->role
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'Data created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if ($user == null) {
            return response()->json([
                'status' => 400,
                'message' => 'There is no user with the id of '.$id
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'message' => 'Data fetched successfully.',
                'data' => $user
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'string|max:255|nullable|unique:users,name',
            'email' => 'email|max:255|nullable|unique:users,email',
            'password' => 'string|max:255|nullable',
            'balance' => 'numeric|nullable',
            'role' => 'numeric|nullable'
        ]);
        $user = User::find($id);
        if ($user == null) {
            return response()->json([
                'status' => 400,
                'message' => 'There is no user with the id of '. $id
            ]);
        }
        else {
            $user->name = $request->name ? $request->name : $user->name;
            $user->email = $request->email ? $request->email : $user->email;
            $user->password = bcrypt($request->password ? $request->password : $user->password);
            $user->balance = $request->balance ? $request->balance : $user->balance;
            $user->role = $request->role ? $request->role : $user->role;
            $user->save();
            return response()->json([
                'status' => 200,
                'message' => 'Data updated successfully.',
                'data' => $user
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user == null) {
            return response()->json([
                'status' => 400,
                'message' => 'There is no user with the id of '.$id
            ]);
        }
        else {
            $user->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Data deleted successfully.'
            ]);
        }
    }

    public static function balTransfer($sender_id, $receiver_id, $amount) {
        $sender = User::find($sender_id);
        $receiver = User::find($receiver_id);

        if ($sender && $receiver) {   
            if ($sender->balance < $amount) {
                return 2;
            }
            else {
                $sender->balance-=$amount;
                $receiver->balance+=$amount;

                $sender->save();
                $receiver->save();
                return 1;
            }
        }
        else {
            return 0;
        }
    }

    public function addBalance(Request $request) {
        $request->validate([
            'balance' => 'numeric|min:1',
        ]);
        $user = $request->user();
        if ($user == null) {
            return response()->json([
                'status' => 400,
                'message' => 'User not found.'
            ]);
        }
        else {
            $user->balance+=$request->balance;
            $user->save();

            return response()->json([
                'status' => 200,
                'message' => 'Balance added successfully.',
                'data' => $user
            ]);
        }

    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'email|max:255',
            'password' => 'string|max:255'
        ]);
        $user = User::where('email', '=', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->role == 1) {
                $token = $user->createToken('verified', ['admin'])->plainTextToken;
            }
            else if ($user->role == 2) {
                $token = $user->createToken('verified', ['host'])->plainTextToken;
            }
            else {
                $token = $user->createToken('verified', ['buyer'])->plainTextToken; 
            }

            return response()->json([
                'status' => 200,
                'message' => 'Login success.',
                'token' => $token
            ]);
        }
        else {
            return response()->json([
                'status' => 400,
                'message' => 'Incorrect email or password.'
            ]);
        }
    }

    public function regBuyer(Request $request) {
        $token = $this::register($request, 3);
        return response()->json([
            'status' => 200,
            'message' => 'Buyer Registeration Finished.',
            'token' => $token
        ]);
    }

    public function regHost(Request $request) {
        $token = $this::register($request, 2);
        return response()->json([
            'status' => 200,
            'message' => 'Host Registeration Finished',
            'token' => $token
        ]);
    }

    public static function register(Request $request, $role) {
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|max:255',
            'password' => 'string|max:255'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $role
        ]);
        
        if ($role == 2) {
            $token = $user->createToken('verified', ['host'])->plainTextToken;
        }
        else {
            $token = $user->createToken('verified', ['buyer'])->plainTextToken;
        }

        return $token;
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Logout success.'
        ]);
    }

    public function unauthorized() {
        return response()->json([
            'status' => 401,
            'message' => 'Unauthorized, cannot access endpoint.'
        ]);
    }

    public function showProfile(Request $request) {
        return response()->json([
            'status' => 200,
            'message' => 'Profile data fetched successfully.',
            'data' => $request->user()
        ]);;
    }

    public function updateProfile(Request $request) {
        $request->validate([
            'name' => 'string|max:255|nullable|unique:users,name',
            'email' => 'email|max:255|nullable|unique:users,email',
            'password' => 'string|max:255|nullable'
        ]);

        $user = $request->user();
        $user->name = $request->name ? $request->name : $user->name;
        $user->email = $request->email ? $request->email : $user->email;
        $user->password = $request->password ? $request->password : $user->password;
        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'Profile updated successfully.',
            'data' => $user
        ]);
    }

    public function deleteProfile(Request $request) {
        $request->user()->tokens()->delete();
        $request->user()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Account deleted successfully.'
        ]);
    }
}
