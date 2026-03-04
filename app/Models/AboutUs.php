<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_image',
        'story_title',
        'story_content',
        'story_image',
        'story_year',
        'story_tagline',
        'mission_title',
        'mission_description',
        'mission_image',
        'vision_title',
        'vision_description',
        'vision_image',
        'values',
        'team_title',
        'team_description',
        'team_members',
        'statistics',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'values' => 'array',
        'team_members' => 'array',
        'statistics' => 'array',
    ];

    /**
     * Get the about us content (singleton pattern)
     */
    public static function getContent()
    {
        $content = self::first();

        if (!$content) {
            $content = self::create([
                'hero_title' => 'Our Story',
                'hero_subtitle' => 'The journey of our bakery',
                'story_title' => 'Our Sweet Beginning',
                'story_content' => 'Founded in 2020, we started with a simple mission: to create joy through delicious, handcrafted cakes.',
                'story_year' => '2020',
                'story_tagline' => 'Where every cake tells a story',
            ]);
        }

        return $content;
    }
}
