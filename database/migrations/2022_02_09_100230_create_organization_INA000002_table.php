<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationINA000002Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_INA000002', function (Blueprint $table) {
            $table->increments('id');
            $table->string('application_tracking_no', 191)->nullable();
            $table->unsignedInteger('form_fill_up_by')->comment('1 -> FORM_FILL_UP_BY_OWN, 2 -> FORM_FILL_UP_BY_UDC_ENTREPRENEUR, 3 -> FORM_FILL_UP_BY_CHAMBER_OR_ASSOCIATION, 4-> FORM_FILL_UP_BY_SME_CLUSTER');

            $table->string('udc_name', 100)->nullable();
            $table->string('udc_loc_district', 191)->nullable();
            $table->string('udc_union', 191)->nullable();
            $table->string('udc_code', 255)->nullable();

            $table->string('chamber_or_association_name', 100)->nullable();
            $table->unsignedMediumInteger('chamber_or_association_loc_district_id')->nullable()->index('organization_INA000002_fk_chamber_or_association_loc_district_id');
            $table->string('chamber_or_association_union', 191)->nullable();
            $table->string('chamber_or_association_code', 255)->nullable();

            $table->string('name', 100);
            $table->string('name_bn', 100)->nullable();
            $table->unsignedInteger('gender')->comment('1 -> MALE, 2 -> FEMALE, 3 -> 3rd Gender');
            $table->date('date_of_birth');
            $table->string('educational_qualification', 191);
            $table->string('nid', 30);
            $table->string('nid_file', 255)->comment('upload pdf of nid');
            $table->string('mobile', 20);
            $table->string('email', 191)->nullable();
            $table->string('entrepreneur_photo', 255);

            $table->string('organization_trade_license_no', 191);
            $table->string('organization_identification_no', 191)->nullable();
            $table->string('organization_name', 191);
            $table->string('organization_address', 191);
            $table->unsignedMediumInteger('organization_loc_district_id')->nullable()->index('organization_INA000002_fk_organization_loc_district_id');
            $table->unsignedMediumInteger('organization_loc_upazila_id')->nullable()->index('organization_INA000002_fk_organization_loc_upazila_id');
            $table->string('organization_domain', 255)->nullable();
            $table->boolean('factory')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->string('factory_address', 191)->nullable();
            $table->unsignedMediumInteger('factory_loc_district_id')->nullable()->index('organization_INA000002_fk_factory_loc_district_id');
            $table->unsignedMediumInteger('factory_loc_upazila_id')->nullable()->index('organization_INA000002_fk_factory_loc_upazila_id');
            $table->string('factory_web_site', 191)->nullable();
            $table->boolean('office_or_showroom')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->boolean('factory_land_own_or_rent')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->unsignedInteger('proprietorship')->comment('1 -> PROPRIETORSHIP_SOLE_PROPRIETORSHIP, 2 -> PROPRIETORSHIP_PARTNERSHIP_PROPRIETORSHIP, 3 -> PROPRIETORSHIP_JOIN_PROPRIETORSHIP');
            $table->string('industry_establishment_year', 4);
            $table->unsignedInteger('trade_licensing_authority')->comment('1 -> TRADE_LICENSING_AUTHORITY_CITY_CORPORATION, 2 -> TRADE_LICENSING_AUTHORITY_MUNICIPALITY, 3 -> TRADE_LICENSING_AUTHORITY_UNION_COUNCIL');
            $table->string('trade_license', 255)->nullable()->comment('upload pdf of trade license');
            $table->string('industry_last_renew_year', 4);
            $table->boolean('tin')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->string('investment_amount', 255);
            $table->string('current_total_asset', 255)->nullable();
            $table->boolean('registered_under_authority')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->text('registered_authority')->nullable();
            $table->boolean('authorized_under_authority')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->text('authorized_authority')->nullable();
            $table->boolean('specialized_area')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->text('specialized_area_name')->nullable();
            $table->boolean('under_sme_cluster')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->string('under_sme_cluster_name', 100)->nullable();
            $table->boolean('member_of_association_or_chamber')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->string('member_of_association_or_chamber_name', 191)->nullable();
            $table->string('sector', 191);
            $table->string('sector_other_name', 191)->nullable();
            $table->string('business_type', 191);
            $table->string('main_product_name', 191);
            $table->text('main_material_description');
            $table->boolean('import')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->text('import_by')->nullable();
            $table->boolean('export_abroad')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->text('export_abroad_by')->nullable();
            $table->string('industry_irc_no', 191)->nullable();
            $table->text('salaried_manpower')->nullable();
            $table->boolean('have_bank_account')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->text('bank_account_type')->nullable();
            $table->boolean('accounting_system')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->boolean('use_computer')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->boolean('internet_connection')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->boolean('online_business')->default(0)->comment('1 -> Yes, 0 -> No');
            $table->string('info_provider_name', 100)->nullable();
            $table->string('info_provider_mobile', 100)->nullable();
            $table->string('info_collector_name', 100)->nullable();
            $table->string('info_collector_mobile', 100)->nullable();
            $table->unsignedTinyInteger('status')->default(0)->comment('0 -> initial state, 1 ->accept, 2->rejected');

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
        Schema::dropIfExists('organization_INA000002');
    }
}
