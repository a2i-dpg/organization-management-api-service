<?php

namespace App\Services;

use App\Models\IndustryAssociationTrade;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class IndustryAssociationTradeService
{

    public function getIndustryAssociationTradeList(Carbon $startTime): array
    {
        /** @var Builder $industryAssociationTradeBuilder */
        $industryAssociationTradeBuilder = IndustryAssociationTrade::select([
            'industry_association_trades.id',
            'industry_association_trades.title',
            'industry_association_trades.title_en',
        ]);
        $industryAssociationTradeBuilder->orderBy('industry_association_trades.id', 'ASC');

        $industryAssociationTrades = $industryAssociationTradeBuilder->get();
        $response['data'] = $industryAssociationTrades->toArray()['data'] ?? $industryAssociationTrades->toArray();
        $response['query_time'] = $startTime->diffInSeconds(Carbon::now());
        return $response;
    }

}
