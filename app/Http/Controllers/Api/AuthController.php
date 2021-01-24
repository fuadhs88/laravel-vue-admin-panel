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



class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
    {
        if (!file_exists(storage_path('installed.json'))) {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|confirmed'
            ]);
            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
            $user->assignRole('Super Admin');
            $user->save();
            File::put(storage_path('installed.json'), "");
            return response()->json([
                'message' => 'Successfully created user!'
            ], 201);
        }
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if (!Auth::guard("web")->attempt($credentials))
            return response()->json([
                'message' => 'Incorrect user or password'
            ], 401);
        $user = $request->user("web");
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    /*     public function sendPasswordResetLink(Request $request)
    {
        return $this->sendResetLinkEmail($request);
    }

    protected function sendResetLinkResponse(Request $request, $response)
    {
        return response()->json([
            'message' => 'Password reset email sent.',
            'data' => $response
        ]);
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response()->json(['message' => 'Email could not be sent to this email address.']);
    }

    public function callResetPassword(Request $request)
    {
        return $this->reset($request);
    }

    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->save();
        event(new PasswordReset($user));
    }

    protected function sendResetResponse(Request $request, $response)
    {
        return response()->json(['message' => 'Password reset successfully.']);
    }

    protected function sendResetFailedResponse(Request $request, $response)
    {
        return response()->json(['message' => 'Failed, Invalid Token.']);
    } */


    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        $profile = $request->user();
        $role = isset($profile->getRoleNames()[0]) ? $profile->getRoleNames()[0] : "";
        $role_id = !empty($role) ? Role::findByName($role)->id : "";
        //$profile->title = $request->getRoleNames()[0];
        return response()->json([
            "id" => $profile["id"],
            "created_at" => $profile["created_at"],
            "name" => $profile["name"],
            "email" => $profile["email"],
            "role_id" => $role_id,
            "role" => $role
        ]);
    }
    public function getAllPermissionsAttribute(Request $request)
    {
        $user = $request->user();
        $role = isset($user->getRoleNames()[0]) ? $user->getRoleNames()[0] : "";
        $isEmpty = empty($role);
        $permissions = !$isEmpty ? Role::findByName($role)->permissions : [];
        if (!$isEmpty) {
            foreach ($permissions as $permission) {
                $permissionsArray[] = $permission->name;
            }
        }
        /* foreach (Permission::all() as $permission) {
            if ($request->user()->can($permission->name)) {
                $permissions[] = $permission->name;
            }
        } */
        return response()->json($permissionsArray);
    }

    public function isInstalled()
    {
        if (!file_exists(storage_path('installed.json'))) {
            return response()->json(["isInstalled" => false]);
        }
        return response()->json(["isInstalled" => true]);
    }
}
