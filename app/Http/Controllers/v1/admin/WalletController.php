<?php

namespace App\Http\Controllers\v1\admin;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\DealerType;
use App\Models\DealerWallet;
use App\Models\TopupRequest;
use App\Models\Tycoon;
use App\Models\TycoonWallet;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function all_dealer_withdraw_requests(){
        try{
            $requests=DealerWithdraw::all();
            return $this->success($requests, 'All withdraw requests from dealers', 200);
        }
        catch (Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    //
    public function update_dealer_withdraw_request_status(Request $req){
        try{
            $request=DealerWithdraw::findOrFail($req->request_id);
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
                    elseif($request->request_from_type=="App\Models\Tycoon"){
                        $tycoon_wallet=TycoonWallet::where('tycoon_id',$request->request_from->id)->first();
                        if($tycoon_wallet){
                            $tycoon_wallet+=$request->amount;
                            $tycoon_wallet->save();
                        }
                        else{
                            TycoonWallet::create([
                                'tycoon_id'=>$request->request_from->id,
                                'product_balance'=>$request->amount
                            ]);
                        }
                        
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
