<?php

namespace App\Services\CommonServices;

use App\Models\IndustryAssociation;
use App\Models\IndustryAssociationCodePessimisticLocking;
use App\Models\IndustryCodePessimisticLocking;
use App\Models\InvoicePessimisticLocking;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;


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

    /**
     * @throws Throwable
     */
    public static function getNewInvoiceCode(string $invoicePrefix, int $invoiceIdSize): string
    {
        $invoice = "";
        DB::beginTransaction();
        try {
            /** @var InvoicePessimisticLocking $existingSSPCode */
            $existingCode = InvoicePessimisticLocking::lockForUpdate()->first();
            $code = !empty($existingCode) && $existingCode->last_incremental_value ? $existingCode->last_incremental_value : 0;
            $code = $code + 1;
            $padSize = $invoiceIdSize - strlen($code);

            /**
             * Prefix+000000N. Ex: EN+Course Code+incremental number
             */
            $invoice = str_pad($invoicePrefix . "I", $padSize, '0', STR_PAD_RIGHT) . $code;

            /**
             * Code Update
             */
            if ($existingCode) {
                $existingCode->last_incremental_value = $code;
                $existingCode->save();
            } else {
                InvoicePessimisticLocking::create([
                    "last_incremental_value" => $code
                ]);
            }
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
        return $invoice;
    }

    public static function paymentSecreteKey(array $payload)
    {
        $token = JWTAuth::fromUser($user);
    }
}
