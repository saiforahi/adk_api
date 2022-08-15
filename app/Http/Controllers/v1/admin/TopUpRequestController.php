<?php

namespace App\Http\Controllers\v1\admin;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\DealerWallet;
use App\Models\TopUpRequest;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopUpRequestController extends Controller
{
    //
    public function all_requests_from_dealers($type){
        try{
            $requests=array();
            switch($type){
                case "dealer":
                    
                    $requests = TopUpRequest::with(['request_from'])->whereHasMorph('request_from',Dealer::class,function(Builder $query){
                        $query->orderBy('created_at', 'desc');
                    })->get();
                    break;

            }
            
            return $this->success($requests, 'data', 200);
        }
        catch (Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    public function topup_dealer($dealer,$balance){
        try{
            
            
            return $this->success($requests, 'data', 200);
        }
        catch (Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    public function update_status(Request $req){
        try{
            $request=TopUpRequest::findOrFail($req->request_id);
            switch($req->status){
                case "APPROVED":
                    $request->status= $req->status;
                    break;
                
                case "PROCESSED":
                    $request->status= $req->status;
                    if($request->request_from_type=="App\Models\Dealer"){
                        $dealer_wallet=DealerWallet::where('dealer_id',$request->request_from->id)->first();
                        $dealer_wallet->product_balance+=$request->amount;
                        $dealer_wallet->save();
                    }

            }
            $request->save();
            return $this->success($request->request_from()->getShortName(), 'data', 200);
        }
        catch (Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
}
