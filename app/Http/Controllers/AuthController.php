<?php

namespace App\Http\Controllers;

use App\Services\Response;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $this->validate($request, $this->rules());

            $user = User::create([
                'username' => $request->username,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'password' => bcrypt($request->password),
                'role' => 'user',
            ]);

            $user->api_token = bcrypt($user->id);
            $user->save();

            return $this->successLogin($user);
        } catch (\Exception $exception) {
            return Response::inputFailed();
        }
    }

    private function rules()
    {
        return [
            'username' => 'required|string|unique:users|min:5|max:12',
            'password' => 'required|string|min:5|max:12',
            'first_name' => 'required|string|regex:/^([a-zA-Z])+$/u|min:2|max:20',
            'last_name' => 'required|string|regex:/^([a-zA-Z])+$/u|min:2|max:20',
        ];
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $user = Auth::user();
            $user->api_token = bcrypt($user->id);
            $user->save();

            return $this->successLogin($user);
        }

        return Response::message('invalid login', 401);
    }

    private function successLogin($user)
    {
        return Response::success([
            'token' => $user->api_token,
            'role' => $user->role,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => $user->username,
        ]);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->api_token = null;
        $user->save();

        return Response::message('logout success');
    }
}
