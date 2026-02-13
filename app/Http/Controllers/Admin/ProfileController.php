<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('admin.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string|max:500',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'bio' => $request->bio,
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return redirect()->route('admin.profile.index')
            ->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('admin.profile.index')
            ->with('success', 'Password changed successfully.');
    }

    public function showSettings()
    {
        $user = auth()->user();
        $timezones = timezone_identifiers_list();
        $languages = [
            'en' => 'English',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
        ];

        return view('admin.profile.settings', compact('user', 'timezones', 'languages'));
    }

    public function settings(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'notification_email' => 'nullable|email',
            'language' => 'nullable|string|in:en,es,fr,de',
            'timezone' => 'nullable|string',
        ]);

        $currentSettings = $user->settings ?? [];

        $newSettings = array_merge($currentSettings, $request->only([
            'notification_email',
            'language',
            'timezone',
        ]));

        $user->settings = $newSettings;
        $user->save();

        return redirect()->route('admin.profile.settings')
            ->with('success', 'Settings updated successfully.');
    }
}
