<?php

namespace Database\Seeders;

use App\Models\NascibBusinessTypeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class NascibBusinessTypeServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        NascibBusinessTypeService::query()->truncate();
        $services = ["তথ্য প্রযুক্তি", "কৃষিভিত্তিক কর্মকাণ্ড", "নির্মাণ শিল্প ও হাউজিং", "বৈদেশিক কর্মসংস্থান বিনোদন শিল্প", "জিনিং অ্যান্ড বেলিং", "হাসপাতাল ও ক্লিনিক", "নিউক্লিয়ার ও এনালাইটিক্যাল", "পর্যটন ও সেবা", "মানব সম্পদ উন্নয়ন", "বিভিন্ন ধরনের টেস্টিং ল্যাবরেটরী", "ফটোগ্রাফি", "টেলিকমিউনিকেশন", "পরিবহন ও যোগাযোগ", "ওয়্যারহাউজ", "ইঞ্জিনিয়ারিং কনসালট্যান্সি।", "ফিলিং স্টেশন", "প্রাইভেট ইনল্যান্ড কনটেইনার ডিপার্ট্মেন্ট এন্ড কনটেইনার ফ্রেইট স্টেশন", "ট্যাংক টার্মিনাল", "চেইন সুপার মার্কেট/শপিংমল", "এ্যাভিয়েশন সার্ভিস", "ইন্সপেকশন এন্ড টেস্টিং সার্ভিস", "আঞ্চলিক ফিডার ভেসেল ও কোস্টাল জাহাজ চলাচল শিল্প", "ড্রাই ডকিং ও জাহাজ মেরামত শিল্প", "মডার্নাইজড় ক্লিনিং সার্ভিস ফর হাইরাইজ এপার্টমেন্টস, কমার্শিয়াল বিল্ডিং", "অটো মোবাইল সার্ভিসিং", "টেকনিক্যাল ভোকেশনাল ইন্সটিটিউটস", "বিজ্ঞাপন শিল্পখাত ও মডেলিং", "মানসম্মত বীজের জন্য গবেষণা এবং উন্নয়ন", "আউট সোর্সিং এবং সিকিউরিটি সার্ভিস", "সমুদ্রগামী জাহাজ চলাচল ব্যবসা", "চলচ্চিত্র শিল্প", "নিউজ পেপার শিল্প"];
        $servicePayload = [];
        foreach ($services as $service) {
            $servicePayload[] = [
                'industry_association_id' => 2,
                'title' => $service
            ];
        }
        NascibBusinessTypeService::insert($servicePayload);
        Schema::enableForeignKeyConstraints();

    }
}
