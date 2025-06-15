<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\User;
use App\Models\Bookmark;
use App\Models\Banned_ip;
use App\Models\UserReading;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Mail\OTPUpdateUserMail;
use App\Models\Countries;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Intervention\Image\Facades\Image;
use App\Services\ReadingHistoryService;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:6|confirmed',
            ], [
                'current_password.required' => 'Password hiện tại là bắt buộc',
                'password.required' => 'Mật khẩu mới là bắt buộc',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            if (!password_verify($request->current_password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => ['current_password' => ['Mật khẩu hiện tại không đúng']],
                ], 422);
            }

            $user->password = bcrypt($request->password);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật mật khẩu thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi cập nhật mật khẩu. Vui lòng thử lại.',
            ], 500);
        }
    }

    public function userProfile()
    {
        $user = Auth::user();

        return view('client.pages.account.profile', compact('user'));
    }


    private function processAndSaveAvatar($imageFile)
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        // Get original file extension
        $extension = $imageFile->getClientOriginalExtension();

        // Create directory if it doesn't exist
        Storage::disk('public')->makeDirectory("avatars/{$yearMonth}");

        // Store original file directly
        $path = $imageFile->storeAs(
            "avatars/{$yearMonth}", 
            "{$fileName}.{$extension}", 
            'public'
        );

        return [
            'original' => $path,
        ];
    }

    public function updateAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            ], [
                'avatar.required' => 'Hãy chọn ảnh avatar',
                'avatar.image' => 'Avatar phải là ảnh',
                'avatar.mimes' => 'Chỉ chấp nhận ảnh định dạng jpeg, png, jpg hoặc gif',
                'avatar.max' => 'Dung lượng avatar không được vượt quá 4MB'
            ]);

            $user = Auth::user();
            DB::beginTransaction();

            try {
                $oldAvatar = $user->avatar;

                $avatarPaths = $this->processAndSaveAvatar($request->file('avatar'));

                $user->avatar = $avatarPaths['original'];
                
                $user->save();

                DB::commit();

                if ($oldAvatar) {
                    Storage::disk('public')->delete($oldAvatar);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Cập nhật avatar thành công',
                    'avatar' => $avatarPaths['original'],
                    'avatar_url' => Storage::url($avatarPaths['original']),
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();

                if (isset($avatarPaths)) {
                    Storage::disk('public')->delete($avatarPaths['original']);
                }

                \Log::error('Avatar update error:', ['error' => $e->getMessage()]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Có lỗi xảy ra, vui lòng thử lại sau'
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|min:2|max:255',
            ], [
                'name.required' => 'Tên đầy đủ là bắt buộc',
                'name.min' => 'Tên đầy đủ phải có ít nhất 2 ký tự',
                'name.max' => 'Tên đầy đủ không được vượt quá 255 ký tự',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $user->name = $request->name;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật hồ sơ thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi cập nhật hồ sơ. Vui lòng thử lại.',
            ], 500);
        }
    }

    public function orders()
    {
        return view('client.pages.account.orders');
    }
}
