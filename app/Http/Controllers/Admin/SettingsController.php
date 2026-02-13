<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = $request->except('_token', '_method');

        foreach ($settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                if ($request->hasFile($key)) {
                    if ($setting->value) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    $path = $request->file($key)->store('settings', 'public');
                    $value = $path;
                }

                if (is_array($value)) {
                    $value = json_encode($value);
                }

                $setting->update(['value' => $value]);
            }
        }

        if ($request->has('opening_hours')) {
            $hours = [];
            foreach ($request->opening_hours as $day => $time) {
                $hours[$day] = $time['open'] . '-' . $time['close'];
            }
            Setting::set('opening_hours', json_encode($hours), 'json', 'hours');
        }

        if ($request->has('delivery_time_slots')) {
            Setting::set('delivery_time_slots', json_encode($request->delivery_time_slots), 'json', 'delivery');
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully!');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $path = $request->file('logo')->store('settings', 'public');
        Setting::set('site_logo', $path, 'image', 'general');

        return response()->json([
            'success' => true,
            'path' => asset('storage/' . $path)
        ]);
    }
}
