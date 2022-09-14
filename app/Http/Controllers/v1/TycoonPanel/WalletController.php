<?php

namespace App\Http\Controllers\v1\TycoonPanel;

use App\Http\Controllers\Controller;
use App\Models\AdminWallet;
use App\Models\BalanceProcess;
use App\Models\TycoonWallet;
use App\Models\Withdraw;
use Exception;
use Illuminate\Http\JsonResponse;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:tycoon'])->except([]);
    }

    // =============================== Balance process Start ========================

    //all balance process list
    public function _all():JsonResponse
    {
        try{
            return $this->success(BalanceProcess::get());
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    // store balance process
    public function _store_balance_process(Request $req)
    {
        $req->validate([
            'amount'=> 'required|numeric',
            'available_amount'=> 'required|numeric',
            'balance_type'=> 'required'
        ]);
        if(Auth::user()->wallet && Auth::user()->wallet[$req->balance_type] < (float)$req->amount){
            return $this->failed(null,'Insuficient balance for process.');
        }

        DB::beginTransaction();
        try{
            $model = BalanceProcess::create([
                'tycoon_id'=> auth()->user()->id,
                'balance_type'=> $req->balance_type,
                'amount'=> $req->amount
            ]);
            $model->save();

            TycoonWallet::updateOrInsert(
                ['tycoon_id' => auth()->user()->id],
                [$req->balance_type => DB::raw($req->balance_type.'-'. $req->amount),'main_balance' => DB::raw('main_balance+'. $req->amount)],
            );

            DB::commit();

            return $this->success($model, 'Balance process successfully');
        }
        catch(Exception $e){
            DB::rollback();
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    // =============================== Balance process Start ========================

    // =============================== Withdraw Section Start ========================
    //all balance process list
    public function _all_withdraw():JsonResponse
    {
        try{
            return $this->success(Withdraw::get());
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    // store balance process
    public function _store_withdraw(Request $req)
    {
        $req->validate([
            'amount'=> 'required|numeric'
        ]);

        if(Auth::user()->wallet && Auth::user()->wallet->main_balance < (float)$req->amount){
            return $this->failed(null,'Insuficient balance for withdraw.');
        }

        $admin_pro_fund = ($req->amount * 10) / 100;
        $tycoon_pro_fund = ($req->amount * 2) / 100;
        $withdraw_amount = $req->amount - ($admin_pro_fund + $tycoon_pro_fund);


        
        DB::beginTransaction();
        try{
            $model = Withdraw::create([
                'tycoon_id'=> auth()->user()->id,
                'amount'=> $withdraw_amount,
                'admin_pro_fund'=> $admin_pro_fund,
                'tycoon_pro_fund'=> $tycoon_pro_fund
            ]);
            $model->save();

            TycoonWallet::updateOrInsert(
                ['tycoon_id' => auth()->user()->id],
                ['main_balance' => DB::raw('main_balance-'. $req->amount), 'provident_fund' => DB::raw('provident_fund+'. $tycoon_pro_fund)]
            );

            AdminWallet::updateOrInsert(
                ['admin_id' => 1],
                ['provident_fund_gap' => DB::raw('provident_fund_gap+'.  $admin_pro_fund)]
            );

            DB::commit();

            return $this->success($model, 'Balance process successfully');
        }
        catch(Exception $e){
            DB::rollback();
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    // =============================== Withdraw Section End ========================
}
