<?php

namespace App\Http\Controllers\v1\setting;

use App\Http\Controllers\Controller;
use App\Models\DealerCommission;
use App\Models\TycoonCommission;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class CommissionController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth:admin'])->except([]);
        // $this->middleware('role:super-admin|unit-admin')->except([]);
    }


    //all supplier list fetch
    public function _all():JsonResponse
    {
        try{

            $data = [];
            $data ['dealer'] = DealerCommission::leftJoin('dealer_types', 'dealer_commissions.dealer_type_id', 'dealer_types.id')->select('dealer_commissions.*', 'dealer_types.name as type_name')->get();
            $data ['tycoon'] = TycoonCommission::first();

            return $this->success($data);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }

    // update supplier details
    public function _update(DealerCommission $dealerCommission,DealerCommissionRequest $req):JsonResponse
    {
        try{
            $data = [];
            if ($req->password) {
                $data=array('password'=>Hash::make($req->password));
            }
            $dealerCommission->update(array_merge($req->except('password'),$data));
            return $this->success($dealerCommission);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
}
