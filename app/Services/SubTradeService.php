<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\SubTrade;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SubTradeService
{

    public function getSubTradeList(array $request, Carbon $startTime): array
    {

        $title = $request['title'] ?? "";
        $titleEn = $request['title_en'] ?? "";
        $tradeId = $request['trade_id'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Builder $industrySubTradeBuilder */
        $industrySubTradeBuilder = SubTrade::select([
            'sub_trades.id',
            'sub_trades.title',
            'sub_trades.title_en',
            'sub_trades.trade_id',
            'trades.title_en as trade_title_en',
            'trades.title as trade_title',

        ]);
        $industrySubTradeBuilder->join('trades', 'sub_trades.trade_id', '=', 'trades.id');
        $industrySubTradeBuilder->orderBy('sub_trades.id', $order);

        if (!empty($titleEn)) {
            $industrySubTradeBuilder->where('sub_trades.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $industrySubTradeBuilder->where('sub_trades.title', 'like', '%' . $title . '%');
        }

        if (is_numeric($tradeId)) {
            $industrySubTradeBuilder->where('sub_trades.trade_id', $tradeId);
        }

        $industrySubTrades = $industrySubTradeBuilder->get();
        $response['data'] = $industrySubTrades->toArray()['data'] ?? $industrySubTrades->toArray();
        $response['query_time'] = $startTime->diffInSeconds(Carbon::now());
        return $response;

    }

    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            'title_en' => 'nullable|max:300|min:2',
            'title' => 'nullable|max:600|min:2',
            'trade_id' => 'nullable|integer',
            'order' => [
                'string',
                'nullable',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
        ]);

    }

}
