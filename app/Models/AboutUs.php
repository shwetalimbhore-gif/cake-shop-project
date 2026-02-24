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

    // Get the first record (since we'll only have one)
    public static function getContent()
    {
        return self::first() ?? new self();
    }
}
