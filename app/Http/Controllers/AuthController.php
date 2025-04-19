<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string'],
            'email' => ['required','email','unique:users'],
            'password' => ['required','min:8', 'confirmed']
        ]);

        // Mã hoá password
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);
        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users'],
            'password' => ['required', 'min:8']
        ]);

        // Lấy thông tin người dùng dựa vào email đã cung cấp
        $user = User::where('email', $data['email'])->first();

        // Kiểm tra xem người dùng có tồn tại và mật khẩu có chính xác hay không
        if (!$user || !Hash::check($data['password'], $user->password)) {
            // Nếu xác thực thất bại, trả về mã lỗi 401 và thông báo
            return response([
                'message' => 'Thông tin không chính xác'
            ], 401);
        }

        // Tạo token cá nhân cho người dùng để xác thực
        $token = $user->createToken('auth_token')->plainTextToken;

        // Trả về phản hồi thành công với thông tin người dùng và token
        return response()->json([
            'message' => "Login Successfully",
            'user' => $user,
            'token' => $token
        ]);
    }

    public function userProfile()
    {
        $userData = Auth::user();
        return response()->json([
            'status' => true,
            'message' => 'User login profile',
            'data' => $userData,
            'id' => $userData->id
        ], status:200);
    }

    public function userResource()
    {
        $userData = Auth::user();
        return response()->json($userData, 200);
    }
    
    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Log out successfully',
            'data' => []
        ], 200);
    }
}
