<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class IndustryAssociationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        Schema::disableForeignKeyConstraints();

        DB::table('industry_associations')->truncate();
        
        \DB::table('industry_associations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => '',
                'trade_id' => 1,
                'title' => 'মেট্রোপলিটন চেম্বার অব কমার্স অ্যান্ড ইন্ড্রাস্ট্রি, ঢাকা',
            'title_en' => 'Metropolitan Chamber of Commerce & Industry, Dhaka (MCCI)',
                'loc_division_id' => 3,
                'loc_district_id' => 18,
                'loc_upazila_id' => NULL,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'google_map_src' => NULL,
            'address' => 'Chamber Building (4th Floor), 122-124, Motijheel CA, Dhaka-1000, Bangladesh',
            'address_en' => 'Chamber Building (4th Floor), 122-124, Motijheel CA, Dhaka-1000, Bangladesh',
                'country' => 'BD',
                'phone_code' => '880',
                'mobile' => '01387788671',
                'email' => 'mcci@gmail.com',
                'fax_no' => NULL,
                'trade_number' => '',
                'name_of_the_office_head' => 'Mr Cooper',
                'name_of_the_office_head_en' => NULL,
                'name_of_the_office_head_designation' => 'CEO',
                'name_of_the_office_head_designation_en' => NULL,
                'contact_person_name' => 'Mr Cooper',
                'contact_person_name_en' => 'Mr Cooper',
                'contact_person_mobile' => '01389898981',
                'contact_person_email' => 'mcci@gmail.com',
                'contact_person_designation' => 'CEO',
                'contact_person_designation_en' => 'CEO',
                'logo' => NULL,
                'domain' => 'mcci.nise.asm',
                'row_status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2022-01-18 16:30:08',
                'updated_at' => '2022-01-30 14:32:15',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => '',
                'trade_id' => 1,
            'title' => 'জাতীয় ক্ষুদ্র ও কুটির শিল্প সমিতি, বাংলাদেশ (নাসিব)',
            'title_en' => 'The National Association of Small and Cottage Industries of Bangladesh (NASCIB)',
                'loc_division_id' => 3,
                'loc_district_id' => 18,
                'loc_upazila_id' => 112,
                'location_latitude' => '23.74587385871404',
                'location_longitude' => '90.41255926982203',
            'google_map_src' => 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d14608.243557006685!2d90.4126242!3d23.745208!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xb533daf0bb8721e0!2sNational%20Association%20of%20Small%20%26%20Cottage%20Industries%20of%20Bangladesh%20(NASCIB)!5e0!3m2!1sen!2sbd!4v1630730307758!5m2!1sen!2sbd',
            'address' => 'Mezbah Uddin Plaza (6th Floor),91, New Circular Road, Mouchak, Dhaka-1217',
            'address_en' => 'Mezbah Uddin Plaza (6th Floor),91, New Circular Road, Mouchak, Dhaka-1217',
                'country' => 'BD',
                'phone_code' => '880',
                'mobile' => '01387788671',
                'email' => 'nascib@gmail.com',
                'fax_no' => NULL,
                'trade_number' => '1234',
                'name_of_the_office_head' => 'sdvsdfvsdv',
                'name_of_the_office_head_en' => NULL,
                'name_of_the_office_head_designation' => 'wsdvdsfvdfv',
                'name_of_the_office_head_designation_en' => NULL,
                'contact_person_name' => 'sdsdfvdvgythgf',
                'contact_person_name_en' => NULL,
                'contact_person_mobile' => '01389898982',
                'contact_person_email' => 'nascib@gmail.com',
                'contact_person_designation' => 'wsdfvweiyutghwef-2',
                'contact_person_designation_en' => NULL,
                'logo' => 'https://nascib.org.bd/wp-content/uploads/2021/09/home-banner-1png.png',
                'domain' => 'nascib.nise.asm',
                'row_status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-01-18 16:33:57',
                'updated_at' => '2022-02-01 15:58:01',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => '',
                'trade_id' => 1,
            'title' => 'ক্ষুদ্র ও মাঝারি শিল্প ফাউন্ডেশন (এসএমইএফ)',
                'title_en' => NULL,
                'loc_division_id' => 4,
                'loc_district_id' => 38,
                'loc_upazila_id' => NULL,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'google_map_src' => NULL,
                'address' => 'Dhaka',
                'address_en' => NULL,
                'country' => 'BD',
                'phone_code' => '880',
                'mobile' => '01733341665',
                'email' => 'xdrazzak@gmail.com',
                'fax_no' => NULL,
                'trade_number' => '',
                'name_of_the_office_head' => 'Abdur Razzak',
                'name_of_the_office_head_en' => NULL,
                'name_of_the_office_head_designation' => 'Abdur Razzak',
                'name_of_the_office_head_designation_en' => NULL,
                'contact_person_name' => 'Abdur Razzak',
                'contact_person_name_en' => NULL,
                'contact_person_mobile' => '01733341665',
                'contact_person_email' => 'xdrazzak@gmail.com',
                'contact_person_designation' => 'Abdur Razzak',
                'contact_person_designation_en' => NULL,
                'logo' => NULL,
                'domain' => NULL,
                'row_status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-01-30 15:44:08',
                'updated_at' => '2022-01-30 15:47:48',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 5,
                'code' => 'INA000001',
                'trade_id' => 1,
                'title' => 'test',
                'title_en' => NULL,
                'loc_division_id' => 3,
                'loc_district_id' => 18,
                'loc_upazila_id' => NULL,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'google_map_src' => NULL,
                'address' => 'asdfasdf',
                'address_en' => NULL,
                'country' => 'BD',
                'phone_code' => '880',
                'mobile' => '01717458695',
                'email' => 'gac@gmail.com',
                'fax_no' => NULL,
                'trade_number' => '',
                'name_of_the_office_head' => 'sss',
                'name_of_the_office_head_en' => NULL,
                'name_of_the_office_head_designation' => 'eee',
                'name_of_the_office_head_designation_en' => NULL,
                'contact_person_name' => 'sdfsdf',
                'contact_person_name_en' => NULL,
                'contact_person_mobile' => '01752458745',
                'contact_person_email' => 'asdf@gmail.com',
                'contact_person_designation' => 'erer',
                'contact_person_designation_en' => NULL,
                'logo' => NULL,
                'domain' => NULL,
                'row_status' => 2,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-02-01 17:04:06',
                'updated_at' => '2022-02-01 17:04:06',
                'deleted_at' => NULL,
            ),
        ));

        Schema::enableForeignKeyConstraints();

        
    }
}