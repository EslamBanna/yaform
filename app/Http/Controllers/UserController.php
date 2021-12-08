<?php

namespace App\Http\Controllers;

use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Validator;
use Auth;


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
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
    public function signup(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string',
                "date_of_birth" => '',
                "gender" => "",
                "country" => "",
                "phone" => "",
                'email' => 'required|email|exists:users,email',
                'password' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);

            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            return $this->returnError('201', 'fail');
        }
    }
    // public function updateInfo()
    // {
    // }
    // public function getUserInfo()
    // {
    //     return response()->json(auth()->user());
    // }
}
