<?php

namespace App\Http\Controllers\v1\TycoonPanel;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStockRequest;
use App\Models\{ TycoonCommissionHistory };
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use DB;
class CommissionController extends Controller
{

    // order list or requests for dealers 
    public function _all(): JsonResponse
    {
        try{
            $orders=array();
            $orders = TycoonCommissionHistory::with('product:id,name', 'from_tycoon', 'to_tycoon')->where('to_tycoon_id', auth()->user()->id)->where('type', 2)->get();
            return $this->success($orders, 'Commission', 200);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
}
