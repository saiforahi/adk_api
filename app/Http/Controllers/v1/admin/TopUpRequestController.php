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

class TopUpRequestController extends Controller
{
    //
    public function all_topup_requests($type){
        try{
            $requests=array();
            
            switch($type){
                case "dealer":
                    $requests = TopupRequest::with(['request_from'])->whereHasMorph('request_from',Dealer::class,function(Builder $query){
                        $query->orderBy('created_at', 'desc');
                    })->get();
                    break;

                case "tycoon":
                    $requests = TopupRequest::with(['request_from'])->whereHasMorph('request_from',Tycoon::class,function(Builder $query){
                        $query->orderBy('created_at', 'desc');
                    })->get();
                    break;
                case "all":
                    $requests = TopupRequest::with(['request_from'])->whereHasMorph('request_from',[Dealer::class,Tycoon::class],function(Builder $query){
                        $query->orderBy('created_at', 'desc');
                    })->get();
                    break;
            }
            foreach($requests as $history){
                switch($history->request_from_type){
                    case 'App\Models\Dealer':
                        $history['request_from']['from_type']='Dealer';
                        $history['request_from']['dealer_type']=DealerType::where('id',$history->request_from['dealer_type_id'])->first()->name;
                        break;
                    case 'App\Models\Admin':
                        $history['request_from']['from_type']='Admin';break;
                    case 'App\Models\Tycoon':
                        $history['request_from']['from_type']='Tycoon';break;
                    case 'App\Models\MasterTycoon':
                        $history['request_from']['from_type']='Master Tycoon';break;
                }
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
            $request=TopupRequest::findOrFail($req->request_id);
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
