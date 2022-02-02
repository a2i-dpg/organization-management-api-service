<?php

namespace App\Services\CommonServices;

use App\Models\IndustryAssociation;
use App\Models\IndustryAssociationCodePessimisticLocking;
use App\Models\IndustryCodePessimisticLocking;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Throwable;

class CodeGenerateService
{

    /**
     * @throws Throwable
     */
    public static function getIndustryAssociationCode(): string
    {
        DB::beginTransaction();
        try {
            /** @var IndustryAssociationCodePessimisticLocking $existingCode */
            $existingCode = IndustryAssociationCodePessimisticLocking::lockForUpdate()->first();
            $lastIncrementalVal = !empty($existingCode) && $existingCode->last_incremental_value ? $existingCode->last_incremental_value : 0;
            $lastIncrementalVal = $lastIncrementalVal + 1;
            $padSize = IndustryAssociation::INDUSTRY_ASSOCIATION_CODE_SIZE - strlen($lastIncrementalVal);

            /**
             * Prefix+000000N. Ex: INA+Incremental Value
             */
            $industryAssoCode = str_pad(IndustryAssociation::INDUSTRY_ASSOCIATION_CODE_PREFIX, $padSize, '0', STR_PAD_RIGHT) . $lastIncrementalVal;

            /**
             * Code Update
             */
            if ($existingCode) {
                $existingCode->last_incremental_value = $lastIncrementalVal;
                $existingCode->save();
            } else {
                IndustryAssociationCodePessimisticLocking::create([
                    "last_incremental_value" => $lastIncrementalVal
                ]);
            }
            DB::commit();
            return $industryAssoCode;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

    }

    /**
     * @throws Throwable
     */
    public static function getIndustryCode(): string
    {
        DB::beginTransaction();
        try {
            /** @var IndustryCodePessimisticLocking $existingCode */
            $existingCode = IndustryCodePessimisticLocking::lockForUpdate()->first();
            $lastIncrementalVal = !empty($existingCode) && $existingCode->last_incremental_value ? $existingCode->last_incremental_value : 0;
            $lastIncrementalVal = $lastIncrementalVal + 1;
            $padSize = Organization::INDUSTRY_CODE_SIZE - strlen($lastIncrementalVal);

            /**
             * Prefix+000000N. Ex: IND+Incremental Value
             */
            $industryCode = str_pad(Organization::INDUSTRY_CODE_PREFIX, $padSize, '0', STR_PAD_RIGHT) . $lastIncrementalVal;

            /**
             * Code Update
             */
            if ($existingCode) {
                $existingCode->last_incremental_value = $lastIncrementalVal;
                $existingCode->save();
            } else {
                IndustryCodePessimisticLocking::create([
                    "last_incremental_value" => $lastIncrementalVal
                ]);
            }
            DB::commit();
            return $industryCode;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

    }
}
