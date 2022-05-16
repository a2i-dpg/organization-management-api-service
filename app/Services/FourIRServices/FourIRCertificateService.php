<?php

namespace App\Services\FourIRServices;

use App\Facade\ServiceToServiceCall;

class FourIRCertificateService
{

    public function getCertificateList(array $params, int $fourIrInitiativeId)
    {
        return ServiceToServiceCall::getFourIrCertificateList($params, $fourIrInitiativeId);
    }
}
