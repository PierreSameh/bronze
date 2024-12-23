<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'distinguishes_en' => 'We offer premium quality and unmatched services.',
            'distinguishes_ar' => 'نحن نقدم جودة ممتازة وخدمات لا مثيل لها.',
            'privacy_en' => 'Your data privacy is our top priority.',
            'privacy_ar' => 'خصوصية بياناتك هي أولويتنا القصوى.',
            'terms_en' => 'Please read and accept our terms and conditions.',
            'terms_ar' => 'يرجى قراءة وقبول الشروط والأحكام.',
            'roles_en' => 'Our platform roles ensure smooth operations.',
            'roles_ar' => 'أدوار منصتنا تضمن سير العمليات بسلاسة.',
            'about_en' => 'We are dedicated to providing the best services.',
            'about_ar' => 'نحن ملتزمون بتقديم أفضل الخدمات.',
            'services_en' => 'Our services include delivery, support, and customization.',
            'services_ar' => 'تشمل خدماتنا التوصيل والدعم والتخصيص.',
        ]);
    }
}
