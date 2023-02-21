<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    public function store(Request $request)
    {
        $authUser = auth()->user();
        $data = $this->formValidate($request);
        //  Note: must check if creator is admin or not
        if ($authUser->is_mangement_team) {
            $data['role'] = 'manger';
            $data['creator_id'] = $authUser->id;
            if ($request->is_mangement) {
                $data['is_mangement_team'] = true;
            }
        } else {
            $data['role'] = 'user';
            $data['parent_id'] = $authUser->parent_id ?? $authUser->id;
        }
        $data['password'] = Hash::make('123456');
        $user = User::create($data);
        if ($user) {
            $this->handlingImgStore($request, $user);
            $user->notify(new VerifyEmailNotification());
            return response()->json(['message' => 'User Was created Successfully!'], 200);
        }
    }
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if ($user) {
            $data = $this->formValidate($request);
            $user = $user->update($data);
            $this->handlingImgStore($request, $user);

            return response()->json(['message' => 'User Was Updated Successfully!'], 200);
        }
        abort(404, 'user not found');
    }
    private function formValidate(Request $request)
    {
        $userId = request()->id ?? null;
        if ($request->is_mangement) {
            $data = $request->validate([
                'full_name' => 'required|max:225',
                'email' => 'required|email|unique:users,email,' . $userId,
                'user_mobile' => 'required',
            ]);
        } else {
            $data = $request->validate([
                'full_name' => 'required|max:225',
                'email' => 'required|email|unique:users,email,' . $userId,
                'user_mobile' => 'required',
                'company_mobile' => 'required',
                'company_name' => 'required',
                'company_address' => 'required',
                'company_description' => 'required|min:100',
            ]);
        }
        return $data;
    }
    private function handlingImgStore(Request $request, $user)
    {
        if ($request->hasFile('user_avatar')) {
            $attachment = $user->attachments()->where('file_name', 'user avatar')->first();
            if ($attachment !== null && $user) {
                Storage::disk()->delete($attachment->file_path);
                $attachment->delete();
            }
            $path = $request->file('user_avatar')->storeAs('uploads/profiles/' . $user->full_name . '-' . $user->id, $request->user_avatar->hashName());
            $user->attachments()->updateOrCreate([
                'file_path' => $path,
                'file_name' => 'user avatar'
            ]);
        }
        if ($request->hasFile('company_logo')) {
            $attachment = $user->attachments()->where('file_name', 'company logo')->first();
            if ($attachment !== null && $user) {
                Storage::disk()->delete($attachment->file_path);
                $attachment->delete();
            }
            $path = $request->file('company_logo')->storeAs('uploads/profiles/' . $user->full_name . '-' . $user->id, $request->company_logo->hashName());
            $user->attachments()->updateOrCreate([
                'file_path' => $path,
                'file_name' => 'company logo'
            ]);
        }
        return $user;
    }
}
