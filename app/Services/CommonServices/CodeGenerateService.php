<?php

namespace App\Services\CommonServices;

use App\Models\IndustryAssociation;
use App\Models\IndustryAssociationCodePessimisticLocking;
use App\Models\IndustryCodePessimisticLocking;
use App\Models\InvoicePessimisticLocking;
use App\Models\Organization;
use Carbon\Carbon;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Self_;
use Throwable;
use ReallySimpleJWT\Token;


class CodeGenerateService
{

    const JWT_SECRETE = '!yc7Re75a$%)Rs$123*';

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

    /**
     * @throws Throwable
     */
    public static function jwtToken(array $customPayload): string
    {
        /**
         * This JWT library imposes strict secret security as follows:
         * the secret must be at least 12 characters in length;
         * contain numbers;
         * upper and lowercase letters;
         * and one of the following special characters *&!@%^#$
         */

        $secret = self::JWT_SECRETE;
        /** 7 days in second*/
        $expireTime = 604800;

        throw_if(!is_numeric($expireTime), new \Exception("Expire time must be numeric"));

        $issuedAt = Carbon::now();
        $expire = $issuedAt->addSeconds($expireTime)->getTimestamp();
        $serverName = request()->getHost();

        $payload = [
            'iat' => $issuedAt->getTimestamp(),         // Issued at: time when the token was generated
            'iss' => $serverName,                       // Issuer
            'nbf' => $issuedAt->getTimestamp(),         // Not before
            'exp' => $expire,                           // Expire
        ];
        Log::info("JWT:" . json_encode([
                $secret,
                $expireTime,
            ]));
        $payload = array_merge($payload, $customPayload);
        return Token::customPayload($payload, $secret);
    }

    public static function verifyJwt(string $token): bool
    {
        $secret = self::JWT_SECRETE;
        return Token::validate($token, $secret);
    }

    /** Return the payload claims */
    public static function jwtPayloadClaims(string $token): array
    {
        $secret = self::JWT_SECRETE;
        return Token::getPayload($token, $secret);
    }

    /** Return the header claims */
    public static function jwtHeaderClaims(string $token): array
    {
        $secret = self::JWT_SECRETE;
        return Token::getHeader($token, $secret);
    }
}
