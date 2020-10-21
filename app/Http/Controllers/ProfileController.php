<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Validator;
use JWTAuth;
use App\User;

class ProfileController extends Controller
{
    /**
     * Create a new ProfileController instance.
     *
     * @return void
     */
    public function __construct() {
        
    }

    /**
     * Get Profile
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $query = User::where('id', '=' , $user->id);
        
        $row = $query->first();

        return $this->respondSuccess($row);        
    }

    /**
     * Update Profile
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request){
       
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'email' => 'email|unique:users,email,'.$user->id,
            'name' => 'string',
            'role' => 'string',
            'prefer_work_hours' => 'numeric|min:0',
            'password' => 'nullable|string|min:6|max:10',
        ]);

        if ($validator->fails()) {
            return $this->respondValidateError($validator->errors()->first());
        }

        $params = $validator->validated();

        if ($request->has('password') && !empty($request->password)) {
            $params['password'] = bcrypt($request->password);
        } else {
            unset($params['password']);
        }

        $user->update($params);

        return $this->respondSuccess($user);
    }
}
