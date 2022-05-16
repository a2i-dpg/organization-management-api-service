<?php

namespace App\Services\FourIRServices;

use App\Facade\ServiceToServiceCall;

class FourIRCertificateService
{

    public function getCertificateList(int $fourIrInitiativeId)
    {
        return ServiceToServiceCall::getFourIrCertificateList($fourIrInitiativeId);
    }
}
