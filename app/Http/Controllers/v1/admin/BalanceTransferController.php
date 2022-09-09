<?php

namespace App\Http\Controllers\v1\admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminWallet;
use App\Models\BalanceTransfer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class BalanceTransferController extends Controller
{
    //
    public function index(Request $request){
        try{
            return $this->success(BalanceTransfer::with(['transfer_to','transfer_from'])->get());
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
            $new_transfer->transfer_to()->associate(Admin::first());
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
    
}
