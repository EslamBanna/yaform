<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Validator;
use Auth;
use JWTAuth;


class UserController extends Controller
{
    use GeneralTrait;
    public function login(Request $request)
    {
        try {
            $rules = [
                'email' => 'required|email',
                'password' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $cardintions = $request->only(['email', 'password']);
            $token = Auth::guard('api-user')->attempt($cardintions);
            if (!$token) {
                return $this->returnError('E001', 'fail');
            }
            $user = Auth::guard('api-user')->user();
            $user->token = $token;
            return $this->returnSuccessMessage($user);
        } catch (\Exception $e) {
            return $this->returnError('201', 'fail');
        }
    }
    public function logout(Request $request)
    {
        try {
            $token = $request->header('authToken');
            if ($token) {
                JWTAuth::setToken($token)->invalidate();
                return $this->returnSuccessMessage('success');
            } else {
                return $this->returnError('200', 'fail');
            }
        } catch (\Exception $e) {
            return $this->returnError('201', 'fail');
        }
    }
    public function signup(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string',
                "date_of_birth" => 'required',
                "gender" => "required|boolean",
                "country" => "required|string",
                "phone" => "required|string",
                'email' => 'required|email|uniqe:users,email',
                'password' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            $photo = '';
            if ($request->hasFile('photo')) {
                $photo  = $this->saveImage($request->photo, 'users');
            }
            User::create([
                'name' => $request->name,
                "date_of_birth" => $request->date_of_birth,
                "gender" => $request->gender,
                "country" => $request->country,
                "phone" => $request->phone,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'photo' => $photo
            ]);
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            return $this->returnError('201', 'fail');
        }
    }
    public function updateUserInfo(Request $request)
    {
        try {
            $user = Auth()->user();
            $photo = '';
            if ($request->hasFile('photo')) {
                $photo  = $this->saveImage($request->photo, 'users');
            }
            $user->update([
                'name' => $request->name ?? $user->name,
                'date_of_birth' => $request->date_of_birth ?? $user->date_of_birth,
                'gender' => $request->gender ?? $user->gender,
                'country' => $request->country ?? $user->country,
                'phone' => $request->phone ?? $user->phone,
                'email' => $request->email ?? $user->email,
                'password' => bcrypt($request->password) ?? $user->password,
                'photo' => $request->photo ?? $photo,
            ]);
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            return $this->returnError('201', 'fail');
        }
    }
    public function getUserInfo()
    {
        return $this->returnData('data', Auth()->user());
    }
}
