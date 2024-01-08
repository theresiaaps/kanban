<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;


class UserController extends Controller
{
    public function index()
    {
        $pageTitle = 'Users List';
        $users = User::all();
        return view('users.index', [
            'pageTitle' => $pageTitle,
            'users' => $users,
        ]);
    }

    public function editRole($id)
    {
        if(Gate::allows('manageUserRoles', Role::class)) {
            $user = User::findOrFail($id);
            $roles = Role::all();
        } else {
            abort(403);
        }

        $pageTitle = 'Edit User Role';        

        return view('users.edit_role', [
            'pageTitle' => $pageTitle,
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function updateRole($id, Request $request)
    {
        if(Gate::allows('manageUserRoles', Role::class)) {
                $user = User::findOrFail($id);
                $user->update([
                'role_id' => $request->role_id,
            ]);
        } else {
            abort(403);
        }       

        return redirect()->route('users.index');
    }

    public function create()
    {
    $pageTitle = 'Add Role';
    $permissions = Permissions::all();

    return view('roles.create',[
        'pageTitle' => $pageTitle,
        'permissions' => $permissions,
          
    ]);
    }
}