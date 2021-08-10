<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;


/**
 * Class OrganizationSeeder
 * @package Database\Seeders
 */
class OrganizationSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        Organization::factory()->count(5)->create();
    }
}
