<?php

namespace App\Http\Controllers\v1\balanceTransfer;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminWallet;
use App\Models\BalanceTransfer;
use App\Models\Dealer;
use App\Models\DealerType;
use App\Models\Tycoon;
use App\Models\TycoonWallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Contracts\Database\Eloquent\Builder;

class BalanceTransferController extends Controller
{
    //
    public function index(Request $request){
        try{
            $histories = BalanceTransfer::with(['transfer_to','transfer_from'])->get();
            foreach($histories as $history){
                switch($history->transfer_from_type){
                    case 'App\Models\Dealer':
                        $history['transfer_from']['from_type']='Dealer';
                        $history['transfer_from']['dealer_type']=DealerType::where('id',$history->transfer_from['dealer_type_id'])->first()->name;
                        break;
                    case 'App\Models\Admin':
                        $history['transfer_from']['from_type']='Admin';break;
                    case 'App\Models\Tycoon':
                        $history['transfer_from']['from_type']='Tycoon';break;
                    case 'App\Models\MasterTycoon':
                        $history['transfer_from']['from_type']='Master Tycoon';break;
                }
                switch($history->transfer_to_type){
                    case 'App\Models\Dealer':
                        $history['transfer_to']['to_type']='Dealer';
                        $history['transfer_to']['dealer_type']=DealerType::where('id',$history->transfer_to['dealer_type_id'])->first()->name;
                        break;
                    case 'App\Models\Admin':
                        $history['transfer_to']['to_type']='Admin';break;
                    case 'App\Models\Tycoon':
                        $history['transfer_to']['to_type']='Tycoon';break;
                    case 'App\Models\MasterTycoon':
                        $history['transfer_to']['to_type']='Master Tycoon';break;
                }

            }
            return $this->success($histories);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    public function add_balance(Request $req){
        $req->validate([
            'amount'=> 'required|numeric',
            'payment_type'=> 'required'
        ]);

        try{

            $new_transfer = BalanceTransfer::create([
                'amount'=> $req->amount,
                'payment_type'=> $req->payment_type,
                'status'=> 'APPROVED'
            ]);

            $new_transfer->transfer_from()->associate(Auth::user());
            $new_transfer->transfer_to()->associate(Admin::find($req->admin_id));
            // $new_transfer->transfer_to()->associate();
            $new_transfer->save();

            // add or distribute amount
            if ($req->payment_type == 1) {
                AdminWallet::updateOrInsert(
                    ['admin_id' => $req->admin_id],
                    ['product_balance' => DB::raw('product_balance+'. $req->amount)]
                );
            } else {
                AdminWallet::updateOrInsert(
                    ['admin_id' => $req->admin_id],
                    ['marketing_balance' => DB::raw('marketing_balance+'. $req->amount)]
                );
            }

            return $this->success(AdminWallet::first(), 'Tycoon wallet updated successfully');
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    public function tycoonHistory(Request $request){
        try{
            return $this->success(BalanceTransfer::with(['transfer_from','transfer_to'])->whereHasMorph('transfer_from', Tycoon::class, function(Builder $query){
                $query->where('id', '=', Auth::user()->id);
            })->get());
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    public function tycoonAddBalance(Request $req){
        $req->validate([
            'amount'=> 'required|numeric'
        ]);
        
        if(Auth::user()->wallet && Auth::user()->wallet->marketing_balance < (float)$req->amount){
            return $this->failed(null,'Insuficient marketing balance.');
        }
        try{
            $new_transfer = BalanceTransfer::create([
                'amount'=> $req->amount,
                'payment_type'=> 1,
                'status'=> 'APPROVED'
            ]);

            $new_transfer->transfer_from()->associate(Auth::user());
            $new_transfer->transfer_to()->associate(Tycoon::find($req->tycoon_id));
            // $new_transfer->transfer_to()->associate();
            $new_transfer->save();

            // add or distribute amount
            TycoonWallet::updateOrInsert(
                ['tycoon_id' => auth()->user()->id],
                ['marketing_balance' => DB::raw('marketing_balance - '. $req->amount)]
            );
         
            TycoonWallet::updateOrInsert(
                ['tycoon_id' => $req->tycoon_id],
                ['product_balance' => DB::raw('product_balance+'. $req->amount)]
            );

            return $this->success(AdminWallet::first(), 'Tycoon wallet updated successfully');
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    
}
