<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GeneralSetting;

class GeneralSettingSeeder extends Seeder
{
    public function run(): void
    {
        GeneralSetting::updateOrCreate(
            ['id' => 1],
            [
                'customer_support_mobile' => '+91 98765 43210',
                'customer_support_email' => 'support@kingster.com',
                'office_time' => 'Mon - Sat: 9:00 AM - 6:00 PM',
                'footer_about_us_1' => 'Kingster is a leading provider of enterprise management solutions, helping businesses streamline their operations and improve efficiency.',
                'footer_about_us_2' => 'Our mission is to empower organizations with innovative technology and exceptional support.',
                'ig_link' => 'https://instagram.com/kingster',
                'wp_link' => 'https://wa.me/919876543210',
                'footer_copyright' => '© 2026 Kingster. All Rights Reserved.',
                'active_clients' => 2500,
                'expert_mechanics' => 50,
                'reputation_years' => 10,
                'first_reviewer_name' => 'John Doe',
                'first_reviewer_msg' => 'Great service and very professional team. Highly recommended!',
                'second_reviewer_name' => 'Jane Smith',
                'second_reviewer_msg' => 'The warranty management system is top-notch. It saved us a lot of time.',
                'third_reviewer_name' => 'Mike Johnson',
                'third_reviewer_msg' => 'Excellent support and easy-to-use interface. Best in the business.',
            ]
        );
    }
}
