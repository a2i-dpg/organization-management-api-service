<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNascibMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nascib_members', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('form_fill_up_by')
                ->comment('1 -> FORM_FILL_UP_BY_OWN, 2 -> FORM_FILL_UP_BY_UDC_ENTREPRENEUR, 3 -> FORM_FILL_UP_BY_CHAMBER_OR_ASSOCIATION, 4-> FORM_FILL_UP_BY_SME_CLUSTER');
            /** Industry information */
            $table->unsignedMediumInteger('industry_association_organization_id')->nullable();
            $table->string('application_tracking_no', 191)->nullable();
            $table->string('trade_license_no', 191);
            /** Common attributes between industry and nascib */
            /** $table->string('title', 500);
             * $table->string('title_en', 191)->nullable();
             * $table->string('address', 1200);
             * $table->string('address_en', 600)->nullable();
             * $table->unsignedMediumInteger('loc_division_id')->nullable()
             * ->index('nascib_member_loc_division_id_inx');
             * $table->unsignedMediumInteger('loc_district_id')->nullable()
             * ->index('nascib_member_loc_district_id_inx');
             * $table->unsignedMediumInteger('loc_upazila_id')->nullable()
             * ->index('nascib_member_loc_upazila_id_inx');
             * $table->string('domain', 255)->nullable(); */
            $table->string('identification_no', 191)->nullable();

            /** Entrepreneur information or contact person information */
            $table->string('entrepreneur_name', 100);
            $table->string('entrepreneur_name_en', 100)->nullable();
            $table->unsignedTinyInteger('entrepreneur_gender')->comment('1 -> MALE, 2 -> FEMALE, 3 -> 3rd Gender');
            $table->date('entrepreneur_date_of_birth');
            $table->string('entrepreneur_educational_qualification', 191);
            $table->string('entrepreneur_nid', 191);
            $table->string('entrepreneur_nid_file_path', 255)->comment('upload pdf of nid');
            $table->string('entrepreneur_mobile', 11);
            $table->string('entrepreneur_email', 191);
            $table->string('entrepreneur_photo_path', 255);
            /** end */

            /** Factory Information */
            $table->unsignedTinyInteger('have_factory')->default(0)
                ->comment('1 -> Yes, 0 -> No');
            $table->string('factory_address', 1200)->nullable();
            $table->string('factory_address_en', 600)->nullable();
            $table->unsignedMediumInteger('factory_loc_division_id')->nullable()
                ->index('nascib_member_loc_division_id_inx');
            $table->unsignedMediumInteger('factory_loc_district_id')->nullable()
                ->index('nascib_member_fk_factory_loc_district_id');
            $table->unsignedMediumInteger('factory_loc_upazila_id')->nullable()
                ->index('nascib_member_fk_factory_loc_upazila_id');

            $table->unsignedTinyInteger('have_office_or_showroom')->default(0)->comment('1 -> Yes, 0 -> No');

            $table->unsignedTinyInteger('have_own_land')->default(0)->comment('1 -> Own Land, 2 -> Rent');
            /** end */

            $table->unsignedInteger('is_proprietorship')->comment('1 -> PROPRIETORSHIP_SOLE_PROPRIETORSHIP, 2 -> PROPRIETORSHIP_PARTNERSHIP_PROPRIETORSHIP, 3 -> PROPRIETORSHIP_JOIN_PROPRIETORSHIP');
            $table->string('date_of_establishment');
            $table->unsignedInteger('trade_licensing_authority')->comment('1 -> TRADE_LICENSING_AUTHORITY_CITY_CORPORATION, 2 -> TRADE_LICENSING_AUTHORITY_MUNICIPALITY, 3 -> TRADE_LICENSING_AUTHORITY_UNION_COUNCIL');
            $table->string('trade_license_path', 255)->nullable()->comment('upload pdf of trade license');
            $table->string('trade_license_last_renew_year', 4);
            $table->unsignedTinyInteger('have_tin')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->string('investment_amount', 255);
            $table->string('current_total_asset', 255)->nullable();
            $table->unsignedTinyInteger('is_registered_under_authority')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->json('registered_authority')->nullable();
            $table->unsignedTinyInteger('authorized_under_authority')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->json('authorized_authority')->nullable();
            $table->unsignedTinyInteger('have_specialized_area')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->json('specialized_area')->nullable();
            $table->unsignedTinyInteger('is_under_sme_cluster')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->unsignedInteger('under_sme_cluster_id')->nullable();
            $table->unsignedTinyInteger('is_under_of_association_or_chamber')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->string('under_association_or_chamber_name', 191)->nullable();
            $table->string('under_association_or_chamber_name_en', 191)->nullable();
            $table->unsignedTinyInteger('sector_id');
            $table->string('other_sector_name', 191)->nullable();
            $table->string('other_sector_name_en', 191)->nullable();
            $table->unsignedTinyInteger('business_type');
            $table->string('main_product_name', 600);
            $table->string('main_product_name_en', 191)->nullable();
            $table->text('main_material_description');
            $table->text('main_material_description_en')->nullable();
            $table->unsignedTinyInteger('is_import')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->json('import_by')->nullable();
            $table->unsignedTinyInteger('is_export_abroad')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->json('export_abroad_by')->nullable();
            $table->string('industry_irc_no', 191)->nullable();
            $table->json('salaried_manpower')->nullable();
            $table->unsignedTinyInteger('have_bank_account')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->json('bank_account_type')->nullable();
            $table->unsignedTinyInteger('have_daily_accounting_system')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->unsignedTinyInteger('use_computer')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->unsignedTinyInteger('have_internet_connection')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->unsignedTinyInteger('have_online_business')->default(0)->comment('1 -> Yes, 0 -> No');

            /** Information Provider Information */
            $table->string('info_provider_name', 100)->nullable();
            $table->string('info_provider_mobile', 100)->nullable();
            $table->string('info_collector_name', 100)->nullable();
            $table->string('info_collector_mobile', 100)->nullable();

            /**Upazila digital center information */
            $table->string('udc_name', 100)->nullable();
            $table->string('udc_loc_district_id', 191)->nullable();
            $table->string('udc_union_id', 191)->nullable();
            $table->string('udc_code', 255)->nullable();

            /** Chamber or Association information */
            $table->string('chamber_or_association_name', 100)->nullable();
            $table->unsignedMediumInteger('chamber_or_association_loc_district_id')->nullable()->index('nascib_member_fk_chamber_or_association_loc_district_id');
            $table->string('chamber_or_association_union_id', 191)->nullable();
            $table->string('chamber_or_association_code', 255)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nascib_members');
    }
}
