<?php

namespace App\Http\Controllers\v1\dealer;

use App\Http\Controllers\Controller;
use App\Http\Requests\WalletTopUpRequest;
use App\Models\Dealer;
use App\Models\DealerWallet;
use App\Models\DealerWithdraw;
use App\Models\TopupRequest;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WalletController extends Controller
{
    //
    public function submit_topup_request(WalletTopUpRequest $req){
        try{
            $new_request = TopupRequest::create($req->all());
            $new_request->request_from()->associate(Auth::user());
            if ($req->hasFile('document') && $req->file('document')->isValid()) {
                $path = request()->file('document')->store('public/topup_request_docs');
                $new_request->document_download_link=env("APP_URL").Storage::url($path);
                $new_request->document=$path;
                $new_request->save();
            }
            return $this->success($new_request, 'data', 200);
        }
        catch (Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }

    public function all_topup_request(){
        try{
            $requests = TopupRequest::with(['request_from'])->whereHasMorph('request_from',Dealer::class,function(Builder $query){
                $query->orderBy('created_at', 'desc');
            })->where('request_from_id',Auth::user()->id)->get();
            return $this->success($requests, 'data', 200);
        }
        catch (Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }

    public function all_withdraw_request(){
        try{
            $requests = DealerWithdraw::where('dealer_id',Auth::user()->id)->get();
            return $this->success($requests, 'data', 200);
        }
        catch (Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }

    public function post_withdraw_request(Request $req){
        try{
            $req->validate([
                'amount'=>'required'
            ]);
            if($req->amount>DealerWallet::where('dealer_id',auth()->user()->id)->first()->profit){
                return $this->failed(null, "You can not withdraw more than profit balance", 403);
            }
            return $this->success(null, 'data', 200);
        }
        catch (Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
}
