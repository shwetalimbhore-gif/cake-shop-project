<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('about_us', function (Blueprint $table) {
            $table->id();

            // Hero Section
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_image')->nullable();

            // Story Section
            $table->string('story_title')->nullable();
            $table->text('story_content')->nullable();
            $table->string('story_image')->nullable();
            $table->string('story_year')->nullable();
            $table->string('story_tagline')->nullable();

            // Mission Section
            $table->string('mission_title')->nullable();
            $table->text('mission_description')->nullable();
            $table->string('mission_image')->nullable();

            // Vision Section
            $table->string('vision_title')->nullable();
            $table->text('vision_description')->nullable();
            $table->string('vision_image')->nullable();

            // Values Section
            $table->json('values')->nullable(); // Store multiple values

            // Team Section
            $table->string('team_title')->nullable();
            $table->text('team_description')->nullable();
            $table->json('team_members')->nullable(); // Store team members

            // Statistics
            $table->json('statistics')->nullable(); // Store stats like years, customers

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('about_us');
    }
};
