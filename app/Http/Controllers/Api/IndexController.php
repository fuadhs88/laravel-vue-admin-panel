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



class IndexController extends Controller
{
    public function showRoutes()
    {
        $routes = [];
        foreach (\Route::getRoutes()->getIterator() as $route) {
            if (strpos($route->uri, 'api') !== false) {
                $routes[] = $route->uri;
            }
        }
        return response()->json($routes);
    }
    public function showRoutesGroup(Request $request)
    {
        $user = $request->user('api');
        $as = "index" || "views";
        if (isset($user)) {
            $role = isset($user->getRoleNames()[0]) ? $user->getRoleNames()[0] : "";
        }
        $isEmpty = empty($role);
        $permissions = !$isEmpty ? Role::findByName($role)->permissions : [];
        $permissionsArray = [];
        if (!$isEmpty) {
            foreach ($permissions as $permission) {
                $permissionsArray[] = "can:$permission->name";
            }
        }
        $basicperms = [];
        if (!isset($user)) {
            $basicperms[] = "api";
        } else {
            $basicperms[] = "api";
            $basicperms[] = "auth:api";
        }
        $name = $permissionsArray;
        $routeCollection = Route::getRoutes(); // RouteCollection object
        $routes = $routeCollection->getRoutes(); // array of route objects
        $grouped_routes = array_filter($routes, function ($route) use ($name, $as, $basicperms) {
            $action = $route->getAction();
            if (isset($action['middleware']) && isset($action['as'])) {
                // for the first level groups, $action['group_name'] will be a string
                // for nested groups, $action['group_name'] will be an array
                if (is_array($action['middleware'])) {
                    if (count(array_intersect($name, $action['middleware'])) !== 0) {
                        return explode("-", $action['as'])[0] == $as;
                    } else
                        return $action['middleware'] == $basicperms && explode("-", $action['as'])[0] == $as;;
                }
            }
            return false;
        });
        $routesArray = [];
        foreach ($grouped_routes as $route) {
            $action = $route->action;
            $otherperms = $action["middleware"];
            $guardsArray = [];
            foreach ($otherperms as $permission) {
                if ($permission !== "api" && $permission !== "auth:api") {
                    $guardsArray[] = explode(":", $permission)[1];
                }
            };
            /*             $permissions = array_values(array_filter($action["middleware"], function ($array) {
                if ($array !== "api" && $array !== "auth:api")
                    return TRUE;
                else
                    return FALSE;
            })); */
            $el = [
                "name" => explode("-", $action['as'])[1],
                "group" => explode("-", $action['as'])[0],
                "uri" => $route->uri,
                "permissions" => $guardsArray
            ];
            $routesArray[] = $el;
        }
        if (empty($routesArray)) {
            return response()->json([]);
        };
        return response()->json($routesArray);
    }
    public function getAllUsers()
    {
        $users = User::with('roles')->get();
        $usersArray = [];
        foreach ($users as $user) {
            $role = isset($user->roles[0]->name) ? $user->roles[0]->name : "";
            $role_id = !empty($role) ? $user->roles[0]->id : "";
            if ($role !== "Super Admin") {
                $el = [
                    "id" => $user["id"],
                    "user_name" => $user["name"],
                    "email" => $user["email"],
                    "created_at" => $user["created_at"],
                    "role" => $role,
                    "role_id" => $role_id
                ];
                $usersArray[] = $el;
            }
        }
        return response()->json($usersArray);
    }

    public function getUser(Request $request, Int $id)
    {
        $user = User::where('id', '=', $id)->with('roles')->with('permissions')->get();
        $user = !empty($user[0]) ? $user[0] : "";
        $role = isset($user->roles[0]->name) ? $user->roles[0]->name : "";
        $isEmpty = empty($role);
        $permissions = !$isEmpty ? Role::findByName($role)->permissions : [];
        $role_id = !empty($role) ? $user->roles[0]->id : "";
        $isAdmin = $role == "Super Admin";
        //$profile->title = $request->getRoleNames()[0];
        $el = [];
        if (!empty($user) && !$isAdmin) {
            $el = [
                "id" => $user["id"],
                "created_at" => $user["created_at"],
                "name" => $user["name"],
                "email" => $user["email"],
                "role_id" => $role_id,
                "role" => $role,
                "permissions" => $permissions
            ];
        }
        if (!empty($el)) {
            $response = $el;
            $responseStatus = 200;
        } else if (
            empty($el) && $isAdmin
        ) {
            $response = "Forbidden: you can't access super-user from this endpoint";
            $responseStatus = 403;
        } else {
            $response = "There is no user with id $id";
            $responseStatus = 404;
        }
        return response()->json($response, $responseStatus);
    }

    public function getAllRoles()
    {
        $roles = Role::with('permissions')->get();
        $perms = Permission::all();
        $rolesArray = [];
        foreach ($roles as $role) {
            $permissionsArray = [];
            foreach ($perms as $permission) {
                $can = $role->hasPermissionTo($permission->name);
                $el = [
                    "id" => $permission["id"],
                    "name" => $permission["name"],
                    "can" => $can
                ];
                $permissionsArray[] = $el;
            }
            $el = [
                "id" => $role["id"],
                "role_name" => $role["name"],
                "created_at" => $role["created_at"],
                "permissions" => $permissionsArray
            ];
            $rolesArray[] = $el;
        }
        return response()->json($rolesArray);
    }
    public function getRoleById(Request $request, Int $id)
    {
        try {
            $role = Role::findById($id);
        } catch (RoleDoesNotExist $e) {
            return response()->json(["message" => "Role does not exist"], 404);
        }

        $perms = Permission::all();
        //$permissions = $role->permissions;
        foreach ($perms as $permission) {
            $can = $role->hasPermissionTo($permission->name);
            $el = [
                "id" => $permission["id"],
                "name" => $permission["name"],
                "can" => $can
            ];
            $permissionsArray[] = $el;
        }
        $el = [
            "id" => $role["id"],
            "name" => $role["name"],
            "created_at" => $role["created_at"],
            "permissions" => $permissionsArray
        ];
        return response()->json($el);
    }

    public function getPermissions()
    {
        $perms = Permission::all();
        foreach ($perms as $permission) {
            $el = [
                "id" => $permission["id"],
                "permission_name" => $permission["name"],
            ];
            $permissionsArray[] = $el;
        }
        return response()->json($permissionsArray);
    }
}
