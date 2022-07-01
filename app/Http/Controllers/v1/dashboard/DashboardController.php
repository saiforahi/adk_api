<?php

namespace App\Http\Controllers\v1\dashboard;

use App\Events\v1\UploadImageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\DealerRequest;
use App\Models\ProductStock;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use DB;
class DashboardController extends Controller
{
    public function index ():JsonResponse
    {
        try{

            $stock = ProductStock::select(DB::raw('sum(qty)', 'sum(price)'));

            
            return $this->success($stock);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
}
