<?php

namespace App\Services;

use App\Models\Trade;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class TradeService
{

    public function getTradeList(Carbon $startTime): array
    {
        /** @var Builder $tradeBuilder */
        $tradeBuilder = Trade::select([
            'trades.id',
            'trades.title',
            'trades.title_en',
        ]);
        $tradeBuilder->orderBy('trades.id', 'ASC');

        $trades = $tradeBuilder->get();
        $response['data'] = $trades->toArray()['data'] ?? $trades->toArray();
        $response['query_time'] = $startTime->diffInSeconds(Carbon::now());
        return $response;
    }

}
