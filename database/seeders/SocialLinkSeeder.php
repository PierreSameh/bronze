<?php

namespace Database\Seeders;

use App\Models\SocialLink;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SocialLink::create([
            'youtube' => 'https://www.youtube.com/example',
            'facebook' => 'https://www.facebook.com/example',
            'pinterest' => 'https://www.pinterest.com/example',
            'instagram' => 'https://www.instagram.com/example',
            'twitter' => 'https://www.twitter.com/example',
            'tiktok' => 'https://www.tiktok.com/@example',
            'email' => 'contact@example.com',
        ]);
    }
}
