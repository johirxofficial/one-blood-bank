<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'about',
                'title' => 'আমাদের সম্পর্কে',
                'content' => '<p>One Blood Bank বাংলাদেশের একটি অলাভজনক ডিজিটাল ব্লাড ডোনার ডাটাবেজ। আমাদের লক্ষ্য — রক্তের প্রয়োজনে দ্রুত, নিরাপদ ও ভেরিফাইড ডোনারের সাথে রোগীকে যুক্ত করা।</p><p>সব ডোনারের ফোন নম্বর যাচাই করা হয়, তাই ভুয়া নম্বর বা দালালের ঝামেলা নেই।</p>',
                'meta_description' => 'One Blood Bank সম্পর্কে জানুন — বাংলাদেশের ভেরিফাইড ব্লাড ডোনার ডাটাবেজ।',
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'গোপনীয়তা নীতি',
                'content' => '<p>ডোনারের ফোন নম্বর শুধুমাত্র লগইন করা ইউজাররা দেখতে পান। প্রতিটি নম্বর দেখার লগ রাখা হয় এবং দৈনিক সীমা প্রয়োগ করা হয়, যাতে স্ক্র্যাপিং বা স্প্যাম কল না হয়।</p><p>আমরা আপনার তথ্য তৃতীয় পক্ষের কাছে বিক্রি করি না।</p>',
                'meta_description' => 'One Blood Bank এর গোপনীয়তা নীতি ও ডেটা সুরক্ষা নিয়ে জানুন।',
            ],
            [
                'slug' => 'terms',
                'title' => 'শর্তাবলী',
                'content' => '<p>ডোনারের তথ্য শুধুমাত্র রক্তের প্রয়োজনে ব্যবহার করা যাবে। কোনো প্রকার স্প্যাম, বিজ্ঞাপন বা হয়রানির উদ্দেশ্যে নম্বর ব্যবহার করলে অ্যাকাউন্ট বন্ধ করা হবে।</p><p>ভুল তথ্য দিলে ডোনার অ্যাকাউন্ট বাতিল করা হবে।</p>',
                'meta_description' => 'One Blood Bank ব্যবহারের শর্তাবলী।',
            ],
            [
                'slug' => 'contact',
                'title' => 'যোগাযোগ',
                'content' => '<p>যেকোনো প্রশ্ন বা সমস্যায় আমাদের ইমেইল করুন: support@thebloodbank.test</p>',
                'meta_description' => 'One Blood Bank এর সাথে যোগাযোগ করুন।',
            ],
        ];

        foreach ($pages as $page) {
            $attributes = ['slug' => $page['slug']];
            Page::query()->updateOrCreate($attributes, $page + ['is_published' => true]);
        }
    }
}
