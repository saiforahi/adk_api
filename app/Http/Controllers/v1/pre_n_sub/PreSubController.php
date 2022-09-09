<?php

namespace App\Http\Controllers\v1\pre_n_sub;

use App\Events\v1\UploadImageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\PreSubDealerRequest;
use App\Models\AdminWallet;
use App\Models\BalanceTransfer;
use App\Models\Tycoon;
use App\Models\SubDealerTypes;
use App\Models\TycoonTypes;
use App\Models\TycoonWallet;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\Auth;

class PreSubController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin'])->except([]);
        // $this->middleware('role:super-admin|unit-admin')->except([]);
    }
    public function create_sub_dealer(PreSubDealerRequest $req){
        try{
            
            $data=array('password'=>Hash::make($req->password), 'user_id'=>date('Y').date('m').date('d').Tycoon::all()->count());
            $new_sub_dealer = Tycoon::create(array_merge($req->except('password'),$data));
          
            return $new_sub_dealer;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
    public function create_pre_dealer(PreSubDealerRequest $req){
        try{
            // dd('working');
            $data=array('password'=>Hash::make($req->password), 'user_id'=>date('Y').date('m').date('d').Tycoon::all()->count());
            $new_pre_dealer = Tycoon::create(array_merge($req->except('password'),$data));
            return $new_pre_dealer;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
    public function _all_sub_dealer_types(){
        try{
            return $this->success(TycoonTypes::all());
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    public function _store(PreSubDealerRequest $req)
    {
        if(Auth::user()->wallet && Auth::user()->wallet->product_balance < (float)$req->opening_balance){
            return $this->failed(null,'Insuficient product balance');
        }

        DB::beginTransaction();
        try{
            $new_pre_or_sub_dealer='';
            $data=array('password'=>Hash::make($req->password), 'user_id'=>date('Y').date('m').date('d').Tycoon::all()->count());
            $new_pre_or_sub_dealer = Tycoon::create(array_merge($req->except('password'),$data));
            
            AdminWallet::where('admin_id', auth()->user()->id)->update(
                ['product_balance' => DB::raw('product_balance-'. $req->opening_balance)]
            );

            TycoonWallet::create(
                [
                    'tycoon_id' => $new_pre_or_sub_dealer->id,
                    'product_balance' => $req->opening_balance
                ],
            );

            $new_transfer = BalanceTransfer::create([
                'amount'=> $req->opening_balance,
                'payment_type'=> 3,
                'status'=> 'APPROVED'
            ]);

            $new_transfer->transfer_from()->associate(Auth::user());
            $new_transfer->transfer_to()->associate(Tycoon::first());
            // $new_transfer->transfer_to()->associate();
            $new_transfer->save();

            $images=array();
            if($req->hasFile('image') && $req->file('image')->isValid()){
                array_push($images,$req->file('image'));
            }
            event(new UploadImageEvent($new_pre_or_sub_dealer,$images,'image'));
            DB::commit();
            if($new_pre_or_sub_dealer){
                return $this->success($new_pre_or_sub_dealer);
            }
            else{
                return $this->failed(null, 'Creation failed', 500);
            }
        }
        catch(Exception $e){
            DB::rollback();
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    
    // product balance store
    public function _update_product_balance(Request $req)
    {
        $req->validate([
            'tycoon_id'=>'required|exists:tycoons,id',
            'amount'=> 'required|numeric',
            'payment_type'=> 'required'
        ]);

        $balance_name = $req->payment_type == 1 ? 'product_balance' : 'marketing_balance';
        if(Auth::user()->wallet && Auth::user()->wallet[$balance_name] < (float)$req->amount){
            return $this->failed(null,'Insuficient balance.');
        }

        try{

            $new_transfer = BalanceTransfer::create([
                'amount'=> $req->amount,
                'payment_type'=> $req->payment_type,
                'status'=> 'APPROVED'
            ]);

            $new_transfer->transfer_from()->associate(Auth::user());
            $new_transfer->transfer_to()->associate(Tycoon::first());
            // $new_transfer->transfer_to()->associate();
            $new_transfer->save();

            // add or distribute amount
            if ($req->payment_type == 1) {
                AdminWallet::where('admin_id', auth()->user()->id)->update(
                    ['product_balance' => DB::raw('product_balance-'. $req->amount)]
                );
                TycoonWallet::updateOrInsert(
                    ['tycoon_id' => $req->tycoon_id],
                    ['product_balance' => DB::raw('product_balance+'. $req->amount)]
                );
            } else {
                AdminWallet::where('admin_id', auth()->user()->id)->update(
                    ['marketing_balance' => DB::raw('marketing_balance-'. $req->amount)]
                );
                TycoonWallet::updateOrInsert(
                    ['tycoon_id' => $req->tycoon_id],
                    ['marketing_balance' => DB::raw('marketing_balance+'. $req->amount)]
                );
            }

            return $this->success(TycoonWallet::with('tycoon')->where('tycoon_id',$req->tycoon_id)->first(), 'Tycoon wallet updated successfully');
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }


    //all supplier list fetch
    public function _all():JsonResponse
    {
        try{
            return $this->success(Tycoon::with('wallet:id,tycoon_id,product_balance')->select('tycoons.*', DB::raw('CONCAT(tycoons.first_name, " ", tycoons.last_name) AS name'
            ))->get());
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }


    // update supplier details
    public function _update($pre_or_sub_dealer_id,PreSubDealerRequest $req):JsonResponse
    {
        try{
            $pre_or_sub_dealer=Tycoon::find($pre_or_sub_dealer_id);
            
            $data = [];
            if ($req->password) {
                $data=array('password'=>Hash::make($req->password));
            }
            $pre_or_sub_dealer->update(array_merge($req->except('password'),$data));
            return $this->success($pre_or_sub_dealer);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }


    //show specific suppler details
    public function _details(Tycoon $dealer):JsonResponse
    {
        try{
            return $this->success($dealer);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    public function _delete(Tycoon $dealer): JsonResponse
    {
        $dealer->delete();
        return $this->success($dealer, 'Dealer Deleted Successfully');
    }
}
