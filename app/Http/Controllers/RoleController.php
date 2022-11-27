<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        if ($roles->isEmpty()) {
            return response()->json([
                'status' => 200,
                'message' => 'Current table is empty.'
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'message' => 'Data fetched successfully.',
                'data' => $roles
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
            'title' => 'string|max:255|unique:roles,title'
        ]);
        Role::create([
            'title' => $request->title
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'Data created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        if ($role == null) {
            return response()->json([
                'status' => 400,
                'message' => 'There is no role with the id of '.$id
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'message' => 'Data fetched successfully.',
                'data' => $role
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'string|max:255|unique:roles,title'
        ]);
        $role = Role::find($id);
        if ($role == null) {
            return response()->json([
                'status' => 400,
                'message' => 'There is no role with the id of '. $id
            ]);
        }
        else {
            $role->title = $request->title;
            $role->save();
            return response()->json([
                'status' => 200,
                'message' => 'Data updated successfully.',
                'date' => $role
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        if ($role == null) {
            return response()->json([
                'status' => 400,
                'message' => 'There is no role with the id of '.$id
            ]);
        }
        else {
            $role->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Data deleted successfully.'
            ]);
        }
    }
}
