<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewUserNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notifiable;
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



class UserController extends Controller
{
    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
            'role' => 'required|string'
        ]);
        try {
            $role = Role::findByName($request->role);
        } catch (RoleDoesNotExist $exception) {
            return response()->json(["message" => "You must provide an existing role"], 400);
        }
        if ($role->name == "Super Admin") {
            return response()->json(["message" => "Can't assign role Super Admin to another user"], 403);
        }
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $user->syncRoles([$role->name]);
        $user->save();
        $array = [
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password
        ];
        $password = $request->password;
        try {
            Notification::send($user, new NewUserNotification($user, $password));
        } catch (\Exception $exception) {
            return response()->json(["message" => "Something went wrong with the email, but the user was created", "error" => $exception->getMessage()], 201);
        }

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }

    public function deleteUser(Request $request, Int $id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return response()->json(["message" => "You must provide an existing user"], 400);
        }
        $role = isset($user->getRoleNames()[0]) ? $user->getRoleNames()[0] : "";
        if ($role == "Super Admin") {
            return response()->json(["message" => "You can't delete the Super Admin!"], 403);
        }
        $user->forceDelete();
        return response()->json(["message" => "User has been successfully deleted"], 200);
    }
    public function editUser(Request $request)
    {
        $id = $request->id;
        $request->validate([
            'id' => 'required|int',
            'name' => 'string',
            'email' => 'string|email|unique:users',
            'password' => 'string',
            'role' => 'string'
        ]);
        $userEdit = [];
        $user = User::find($id);
        if (empty($user)) {
            return response()->json(["message" => "You must provide an existing user"], 400);
        }
        if ($request->filled('name')) {
            $userEdit['name'] = $request->input('name');
        }

        if ($request->filled('email')) {
            $userEdit['email'] = $request->input('email');
        }

        if ($request->filled('role')) {
            $req = $request->user();
            $reqRole = isset($req->getRoleNames()[0]) ? $req->getRoleNames()[0] : "";
            $userRole = isset($user->getRoleNames()[0]) ? $user->getRoleNames()[0] : "";
            try {
                $role = Role::findByName($request->role);
            } catch (RoleDoesNotExist $exception) {
                return response()->json(["message" => "You must provide an existing role"], 400);
            }
            if ($userRole == $reqRole) {
                return response()->json(["message" => "You can't change your own role"], 400);
            }
            if ($role->name == "Super Admin") {
                return response()->json(["message" => "Can't assign role Super Admin to another user"], 403);
            }
            $user->syncRoles([$request->input('role')]);
        }

        if ($request->filled('password')) {
            $userEdit['password'] = bcrypt($request->input('password'));
        }

        $user->update($userEdit);
        return response()->json(["message" => "User has been successfully updated"], 200);
    }
}
