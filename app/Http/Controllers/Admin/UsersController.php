<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{

    public function index()
    {
        $users = User::all();
        return view('admin.users.index', ['users' => $users]);
    }


    public function create()
    {
        return view('admin.users.create');
    }



    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'avatar' => 'nullable|image'
        ]);
        $tag = User::create($request->all());
        return redirect()->route('users.index');
    }


    public function edit($id)
    {
        $user = User::find($id);
        return view('admin.users.edit', ['user'=>$user]);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, ['title'=>'required']);
        $user = User::find($id);
        $user->update($request->all());
        return redirect()->route('users.index');
    }


    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index');
    }
}
