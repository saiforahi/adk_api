<?php

namespace App\Http\Controllers\v1\setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\DealerCommissionRequest;
use App\Models\DealerBonusConfig;
use App\Models\TycoonGroupBonusConfig;
use App\Models\TycoonStarMonthlyBonusConfig;
use App\Models\TycoonBonusConfig;
use App\Models\TycoonCommissionHistory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class CommissionController extends Controller
{
    //
    public function __construct()
    {
        // $this->middleware(['auth:admin'])->except([]);
        // $this->middleware('role:super-admin|unit-admin')->except([]);
    }


    //all supplier list fetch
    public function _all():JsonResponse
    {
        try{

            $data = [];
            $data ['dealer_bonus'] = DealerBonusConfig::leftJoin('dealer_types', 'dealer_bonus_configs.dealer_type_id', 'dealer_types.id')->select('dealer_bonus_configs.*', 'dealer_types.name as type_name')->get();
            $data ['tycoon_bonus'] = TycoonBonusConfig::get();
            $data ['tycoon_group_bonus'] = TycoonGroupBonusConfig::get();
            $data ['tycoon_star_monthly_bonus'] = TycoonStarMonthlyBonusConfig::get();

            return $this->success($data);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    //all commission history for admin
    public function _all_admin():JsonResponse
    {
        try{
            $data = TycoonCommissionHistory::with('product:id,name','tycoon:id,first_name,last_name')->get();
            return $this->success($data);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    // update supplier details
    public function _update(DealerCommissionRequest $dealerCommission, DealerCommissionRequest $req):JsonResponse
    {
        try{
            $data = [];
            if ($req->password) {
                $data=array('password'=>Hash::make($req->password));
            }
            $dealerCommission->update(array_merge($req, $data));
            return $this->success($dealerCommission);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
}
