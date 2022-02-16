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

            /** Industry information */
            $table->unsignedMediumInteger('organization_id')->nullable();
            $table->string('application_tracking_no', 191)->nullable();

            /** Common attributes between industry and nascib */
            $table->string('title', 500);
            $table->string('title_en', 191)->nullable();
            $table->string('address', 1200);
            $table->string('address_en', 600)->nullable();
            $table->unsignedMediumInteger('loc_division_id')->nullable()
                ->index('nascib_member_loc_division_id_inx');
            $table->unsignedMediumInteger('loc_district_id')->nullable()
                ->index('nascib_member_loc_district_id_inx');
            $table->unsignedMediumInteger('loc_upazila_id')->nullable()
                ->index('nascib_member_loc_upazila_id_inx');
            $table->string('domain', 255)->nullable();

            $table->string('trade_license_no', 191);
            $table->string('identification_no', 191)->nullable();


            /** Entrepreneur information or contact person information */
            $table->string('entrepreneur_name', 100);
            $table->string('entrepreneur_name_en', 100)->nullable();
            $table->unsignedInteger('entrepreneur_gender')->comment('1 -> MALE, 2 -> FEMALE, 3 -> 3rd Gender');
            $table->date('entrepreneur_date_of_birth');
            $table->string('entrepreneur_educational_qualification', 191);
            $table->string('entrepreneur_nid', 30);
            $table->string('entrepreneur_nid_file_path', 255)->comment('upload pdf of nid');
            $table->string('entrepreneur_mobile', 20);
            $table->string('entrepreneur_email', 191);
            $table->string('entrepreneur_photo_path', 255);
            /** end */

            /** Factory Information */
            $table->boolean('is_factory')->default(0)
                ->comment('1 -> Yes, 0 -> No');
            $table->string('factory_address', 1200)->nullable();
            $table->string('factory_address_en', 600)->nullable();
            $table->unsignedMediumInteger('loc_division_id')->nullable()
                ->index('nascib_member_loc_division_id_inx');
            $table->unsignedMediumInteger('factory_loc_district_id')->nullable()
                ->index('nascib_member_fk_factory_loc_district_id');
            $table->unsignedMediumInteger('factory_loc_upazila_id')->nullable()
                ->index('nascib_member_fk_factory_loc_upazila_id');
            $table->string('factory_web_site', 191)->nullable();
            $table->boolean('office_or_showroom')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->boolean('factory_land_own_or_rent')->default(0)->comment('1 -> Yes, 0 -> No');
            /** end */

            $table->unsignedInteger('is_proprietorship')->comment('1 -> PROPRIETORSHIP_SOLE_PROPRIETORSHIP, 2 -> PROPRIETORSHIP_PARTNERSHIP_PROPRIETORSHIP, 3 -> PROPRIETORSHIP_JOIN_PROPRIETORSHIP');
            $table->string('industry_establishment_year', 4);
            $table->unsignedInteger('trade_licensing_authority')->comment('1 -> TRADE_LICENSING_AUTHORITY_CITY_CORPORATION, 2 -> TRADE_LICENSING_AUTHORITY_MUNICIPALITY, 3 -> TRADE_LICENSING_AUTHORITY_UNION_COUNCIL');
            $table->string('trade_license', 255)->nullable()->comment('upload pdf of trade license');
            $table->string('industry_last_renew_year', 4);
            $table->boolean('is_tin')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->string('investment_amount', 255);
            $table->string('current_total_asset', 255)->nullable();
            $table->boolean('is_registered_under_authority')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->text('registered_authority')->nullable();
            $table->boolean('authorized_under_authority')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->text('authorized_authority')->nullable();
            $table->boolean('have_specialized_area')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->text('specialized_area_name')->nullable();
            $table->boolean('under_sme_cluster')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->string('under_sme_cluster_name', 100)->nullable();
            $table->boolean('have_member_of_association_or_chamber')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->string('member_of_association_or_chamber_name', 191)->nullable();
            $table->string('sector', 191);
            $table->string('sector_other_name', 191)->nullable();
            $table->string('business_type', 191);
            $table->string('main_product_name', 191);
            $table->text('main_material_description');
            $table->boolean('is_import')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->text('import_by')->nullable();
            $table->boolean('is_export_abroad')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->text('export_abroad_by')->nullable();
            $table->string('industry_irc_no', 191)->nullable();
            $table->text('salaried_manpower')->nullable();
            $table->boolean('have_bank_account')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->text('bank_account_type')->nullable();
            $table->boolean('have_accounting_system')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->boolean('use_computer')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->boolean('have_internet_connection')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->boolean('have_online_business')->default(0)->comment('1 -> Yes, 0 -> No');

            $table->unsignedInteger('form_fill_up_by')
                ->comment('1 -> FORM_FILL_UP_BY_OWN, 2 -> FORM_FILL_UP_BY_UDC_ENTREPRENEUR, 3 -> FORM_FILL_UP_BY_CHAMBER_OR_ASSOCIATION, 4-> FORM_FILL_UP_BY_SME_CLUSTER');

            /**Upazila digital center information */
            $table->string('udc_name', 100)->nullable();
            $table->string('udc_loc_district', 191)->nullable();
            $table->string('udc_union', 191)->nullable();
            $table->string('udc_code', 255)->nullable();

            /** Chamber or Association information */
            $table->string('chamber_or_association_name', 100)->nullable();
            $table->unsignedMediumInteger('chamber_or_association_loc_district_id')->nullable()->index('nascib_member_fk_chamber_or_association_loc_district_id');
            $table->string('chamber_or_association_union', 191)->nullable();
            $table->string('chamber_or_association_code', 255)->nullable();

            /** Information Provider Information */
            $table->string('info_provider_name', 100)->nullable();
            $table->string('info_provider_mobile', 100)->nullable();
            $table->string('info_collector_name', 100)->nullable();
            $table->string('info_collector_mobile', 100)->nullable();


            $table->unsignedTinyInteger('row_status')
                ->default(1)
                ->comment('0 => Inactive, 1 => Approved, 2 => Pending, 3 => Rejected');

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
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
