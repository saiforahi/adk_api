<?php

namespace App\Http\Controllers\v1\admin;

use App\Http\Controllers\Controller;
use App\Models\DealerWallet;
use App\Models\DealerWithdraw;
use App\Models\TycoonWallet;
use Exception;
use Illuminate\Http\Request;


class WalletController extends Controller
{
    public function all_dealer_withdraw_requests(){
        try{
            $requests=DealerWithdraw::with(['dealer','dealer.type'])->get();
            return $this->success($requests, 'All withdraw requests from dealers', 200);
        }
        catch (Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    //
    public function update_dealer_withdraw_request_status(Request $req){
        try{
            $req->validate([
                'request_id'=>'required|exists:dealer_withdraws,id',
                'status'=>'required'
            ]);
            $request=DealerWithdraw::findOrFail($req->request_id);
            switch($req->status){
                case "APPROVED":
                    $request->status= $req->status;
                    break;
                
                case "PROCESSED":
                    $request->status= $req->status;
                    $dealer_wallet=DealerWallet::where('dealer_id',$request->dealer_id)->first();
                    $dealer_wallet->profit-=$request->amount;
                    $dealer_wallet->save();
                    break;

            }
            $request->save();
            return $this->success($request, 'Withdraw request updated!', 200);
        }
        catch (Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
}
