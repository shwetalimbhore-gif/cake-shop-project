<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutUsController extends Controller
{
    /**
     * Show the about us page editor
     */
    public function index()
    {
        $about = AboutUs::getContent();
        return view('admin.about.index', compact('about'));
    }

    /**
     * Update about us content
     */
    public function update(Request $request)
    {
        $about = AboutUs::getContent();

        $validated = $request->validate([
            // Hero Section
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max

            // Story Section
            'story_title' => 'nullable|string|max:255',
            'story_content' => 'nullable|string',
            'story_year' => 'nullable|string|max:50',
            'story_tagline' => 'nullable|string|max:255',
            'story_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',

            // Mission Section
            'mission_title' => 'nullable|string|max:255',
            'mission_description' => 'nullable|string',
            'mission_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',

            // Vision Section
            'vision_title' => 'nullable|string|max:255',
            'vision_description' => 'nullable|string',
            'vision_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',

            // Values Section
            'values' => 'nullable|array',
            'values.*.title' => 'nullable|string|max:100',
            'values.*.description' => 'nullable|string',
            'values.*.icon' => 'nullable|string|max:50',

            // Team Section
            'team_title' => 'nullable|string|max:255',
            'team_description' => 'nullable|string',
            'team_members' => 'nullable|array',
            'team_members.*.name' => 'nullable|string|max:100',
            'team_members.*.position' => 'nullable|string|max:100',
            'team_members.*.bio' => 'nullable|string',
            'team_members.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',

            // Statistics
            'statistics' => 'nullable|array',
            'statistics.*.label' => 'nullable|string|max:100',
            'statistics.*.value' => 'nullable|string|max:50',
            'statistics.*.suffix' => 'nullable|string|max:20',

            // SEO
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        // Handle file uploads
        $imageFields = ['hero_image', 'story_image', 'mission_image', 'vision_image'];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old image
                if ($about->$field) {
                    Storage::disk('public')->delete($about->$field);
                }

                $path = $request->file($field)->store('about', 'public');
                $validated[$field] = $path;
            }
        }

        // Handle team member images
        if ($request->has('team_members')) {
            $teamMembers = $request->team_members;

            if ($about->team_members) {
                $existingMembers = $about->team_members;

                foreach ($teamMembers as $index => &$member) {
                    if (isset($member['image']) && $member['image'] instanceof \Illuminate\Http\UploadedFile) {
                        // Upload new image
                        $path = $member['image']->store('about/team', 'public');
                        $member['image'] = $path;

                        // Delete old image if exists
                        if (isset($existingMembers[$index]['image'])) {
                            Storage::disk('public')->delete($existingMembers[$index]['image']);
                        }
                    } elseif (isset($existingMembers[$index]['image'])) {
                        // Keep existing image
                        $member['image'] = $existingMembers[$index]['image'];
                    }
                }
            }

            $validated['team_members'] = $teamMembers;
        }

        // Handle values
        if ($request->has('values')) {
            $validated['values'] = $request->values;
        }

        // Handle statistics
        if ($request->has('statistics')) {
            $validated['statistics'] = $request->statistics;
        }

        // Update or create
        if ($about->exists) {
            $about->update($validated);
        } else {
            $about = AboutUs::create($validated);
        }

        return redirect()->route('admin.about.index')
            ->with('success', 'About Us page updated successfully!');
    }
}
