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
        $user = $request->user();
        $as = "index";
        $role = isset($user->getRoleNames()[0]) ? $user->getRoleNames()[0] : "";
        $isEmpty = empty($role);
        $permissions = !$isEmpty ? Role::findByName($role)->permissions : [];
        $permissionsArray = [];
        if (!$isEmpty) {
            foreach ($permissions as $permission) {
                $permissionsArray[] = "can:$permission->name";
            }
        }
        $name = $permissionsArray;
        $routeCollection = Route::getRoutes(); // RouteCollection object
        $routes = $routeCollection->getRoutes(); // array of route objects
        $grouped_routes = array_filter($routes, function ($route) use ($name, $as) {
            $action = $route->getAction();
            if (isset($action['middleware']) && isset($action['as'])) {
                // for the first level groups, $action['group_name'] will be a string
                // for nested groups, $action['group_name'] will be an array
                if (is_array($action['middleware'])) {
                    if (count(array_intersect($name, $action['middleware'])) !== 0) {
                        return explode("-", $action['as'])[0] == $as;
                    } else return $action['middleware'] == ['api', 'auth:api'] && explode("-", $action['as'])[0] == $as;;
                } else {
                    return $action['middleware'] == $name && explode("-", $action['as'])[0] == $as;
                }
            }
            return false;
        });

        foreach ($grouped_routes as $route) {
            $action = $route->action;
            $standard = ["api", "auth:api"];
            $otherperms = $action["middleware"];
            $permissionsArray = [];
            foreach ($otherperms as $permission) {
                if ($permission !== "api" && $permission !== "auth:api") {
                    $permissionsArray[] = explode(":", $permission)[1];
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
                "permissions" => $permissionsArray
            ];
            $routesArray[] = $el;
        }
        return response()->json($routesArray);
    }
    public function getAllUsers(Request $request)
    {
        $users = User::where('id', '!=', $request->user()->id)->with('roles')->get();
        foreach ($users as $user) {
            $role = isset($user->roles[0]->name) ? $user->roles[0]->name : "";
            $role_id = !empty($role) ? $user->roles[0]->id : "";
            $el = [
                "id" => $user["id"],
                "name" => $user["name"],
                "email" => $user["email"],
                "created_at" => $user["created_at"],
                "role" => $role,
                "role_id" => $role_id
            ];
            $usersArray[] = $el;
        }
        return response()->json($usersArray);
    }

    public function getUser(Int $id)
    {
        $user = User::where('id', '=', $id)->with('roles')->get()[0];
        $role = isset($user->roles[0]->name) ? $user->roles[0]->name : "";
        $role_id = !empty($role) ? $user->roles[0]->id : "";
        //$profile->title = $request->getRoleNames()[0];

        $el = [
            "id" => $user["id"],
            "created_at" => $user["created_at"],
            "name" => $user["name"],
            "email" => $user["email"],
            "role_id" => $role_id,
            "role" => $role
        ];
        return response()->json($el);
    }

    public function getAllRoles(Request $request)
    {
        $roles = Role::with('permissions')->get();
        $perms = Permission::all();
        foreach ($roles as $role) {
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
            $rolesArray[] = $el;
        }
        return response()->json($rolesArray);
    }
    public function getRole(Int $id)
    {
        $role = Role::findById($id);
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
                "name" => $permission["name"],
            ];
            $permissionsArray[] = $el;
        }
        return response()->json($permissionsArray);
    }
}
