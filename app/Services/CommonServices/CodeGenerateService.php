<?php

namespace App\Services\CommonServices;

use App\Models\IndustryAssociation;
use App\Models\IndustryAssociationCodePessimisticLocking;
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
            $code = !empty($existingCode) && $existingCode->last_incremental_value ? $existingCode->last_incremental_value : 0;
            $code = $code + 1;
            $padSize = IndustryAssociation::INDUSTRY_ASSOCIATION_CODE_SIZE - strlen($code);

            /**
             * Prefix+000000N. Ex: INA+Incremental Value
             */
            $sspCode = str_pad(IndustryAssociation::INDUSTRY_ASSOCIATION_CODE_PREFIX, $padSize, '0', STR_PAD_RIGHT) . $code;

            /**
             * Code Update
             */
            if ($existingCode) {
                $existingCode->last_incremental_value = $code;
                $existingCode->save();
            } else {
                IndustryAssociationCodePessimisticLocking::create([
                    "last_incremental_value" => $code
                ]);
            }
            DB::commit();
            return $sspCode;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

    }
}
