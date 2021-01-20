<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Carbon\Carbon;
use App\User;
use Hash;
use Illuminate\Auth\Events\PasswordReset;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Exceptions\InvalidOrderException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Exceptions\RoleDoesNotExist;



class RoleController extends Controller
{
    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'permissions' => 'required|array'
        ]);
        try {
            Role::findByName($request->name);
        } catch (RoleDoesNotExist $exception) {
            $wantPermissions = $request->permissions;
            $perms = Permission::all();
            $permissionsArray = [];
            foreach ($perms as $permission) {
                foreach ($wantPermissions as $wanted) {
                    if ($permission["name"] == $wanted["name"] && $wanted["want"] == true) {
                        $el = $permission["id"];
                        $permissionsArray[] = $el;
                    }
                }
            };
            $role = Role::create(['name' => $request->name]);
            $role->syncPermissions($permissionsArray);
            return response()->json([
                'message' => 'Successfully created role!'
            ], 201);
        };
        return response()->json([
            'message' => 'A role with this name already exists!'
        ], 402);
    }

    public function deleteRole(Request $request)
    {
        $request->validate([
            'id' => 'required|int'
        ]);
        $id = $request->id;
        try {
            $role = Role::findById($id);
        } catch (RoleDoesNotExist $exception) {
            return response()->json([
                'message' => 'A role with this id doesn\'t exist'
            ], 404);
        }
        if ($role->name == "Super Admin") {
            return response()->json([
                'message' => 'You can\'t delete the Super Admin!'
            ], 403);
        }
        $users = User::role($role->name)->get();
        if (count($users) > 0) {
            return response()->json([
                'message' => 'Please reassign or remove all the users with this role before deleting it'
            ], 400);
        }
        $role->delete();
        return response()->json([
            'message' => 'Successfully deleted role!'
        ], 200);
    }

    public function editRole(Request $request)
    {
        $request->validate([
            'id' => 'required|int',
            'permissions' => 'required|array'
        ]);
        $id = $request->id;
        try {
            $role = Role::findById($id);
        } catch (RoleDoesNotExist $exception) {
            return response()->json([
                'message' => 'A role with this id doesn\'t exist'
            ], 404);
        }
        if ($role->name == "Super Admin") {
            return response()->json([
                'message' => 'You can\'t edit the Super Admin!'
            ], 403);
        }
        $userRole = $request->user()->getRoleNames()[0];
        $userRoleId = !empty($userRole) ? Role::findByName($userRole)->id : "";
        if ($id == $userRoleId) {
            return response()->json([
                'message' => 'You can\'t edit your own role!'
            ], 403);
        }

        $wantPermissions = $request->permissions;
        $perms = Permission::all();
        $permissionsArray = [];
        foreach ($perms as $permission) {
            foreach ($wantPermissions as $wanted) {
                if ($permission["name"] == $wanted["name"] && $wanted["want"] == true) {
                    $el = $permission["id"];
                    $permissionsArray[] = $el;
                }
            }
        };

        $role->syncPermissions($permissionsArray);

        return response()->json([
            'message' => "Successfully updated role $role->name!"
        ], 200);
    }
}
