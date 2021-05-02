<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:20',
                'nickname' => 'required|string|max:30',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:10|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{10,}/',
                'phone' => 'required|regex:/^\d{3}-\d{3,4}-\d{4}$/|max:20',
            ]
        );
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        $user = User::create($request->toArray());
        $token = $user->createToken('Access Token')->accessToken;
        $response = ['token' => $token];
        return response($response, 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:10',
            ]
        );
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Access Token')->accessToken;
                $response = ['token' => $token];
                return response($response, 200);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" => 'User does not exist'];
            return response($response, 422);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

    public function search(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'string|max:20',
                'email' => 'string|email|max:255',
                'page' => 'integer|min:1',
                'limit' => 'integer|min:1',
            ]
        );
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $query = User::query();

        if ($request->name) {
            $query->orWhere('name', 'like', '%' . $request->name . '%');
        }

        if ($request->email) {
            $query->orWhere('email', '=', $request->email);
        }
        $limit = 2;
        if ($request->limit) {
            $limit = $request->limit;
        }

        $page = 0;
        if ($request->page) {
            $page = ($request->page - 1) * $limit;
        }
        $query->offset($page)->limit($limit)->get();

        $resultUser = $query->get();
        if (empty($resultUser)) {
            $response = ["message" => 'User does not exist'];
            return response($response, 422);
        }
        return $resultUser;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if (empty($user)) {
            $response = ["message" => 'User does not exist'];
            return response($response, 422);
        }
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
