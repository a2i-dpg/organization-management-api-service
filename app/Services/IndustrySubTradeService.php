<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\IndustrySubTrade;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IndustrySubTradeService
{


    public function getIndustrySubTradeList(array $request, Carbon $startTime): array
    {

        $title = $request['title'] ?? "";
        $titleEn = $request['title_en'] ?? "";
        $IndustryAssociationTradeId = $request['industry_association_trade_id'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Builder $industrySubTradeBuilder */
        $industrySubTradeBuilder = IndustrySubTrade::select([
            'industry_sub_trades.id',
            'industry_sub_trades.title',
            'industry_sub_trades.title_en',
            'industry_sub_trades.industry_association_trade_id',
            'industry_sub_trades.industry_association_trade_id',
        ]);
        $industrySubTradeBuilder->join('industry_association_trades','industry_sub_trades.industry_association_trade_id','=','industry_association_trades.id');
        $industrySubTradeBuilder->orderBy('industry_sub_trades.id', $order);

        if (!empty($titleEn)) {
            $industrySubTradeBuilder->where('industry_sub_trades.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $industrySubTradeBuilder->where('industry_sub_trades.title', 'like', '%' . $title . '%');
        }

        if (is_numeric($IndustryAssociationTradeId)) {
            $industrySubTradeBuilder->where('industry_sub_trades.industry_association_trade_id', $IndustryAssociationTradeId);
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
            'industry_association_trade_id' => 'nullable|integer',
            'order' => [
                'string',
                'nullable',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
        ]);

    }

}
