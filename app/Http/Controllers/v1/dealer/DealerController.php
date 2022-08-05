<?php

namespace App\Http\Controllers\v1\dealer;

use App\Events\v1\UploadImageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\DealerRequest;
use App\Models\Dealer;
use App\Models\DealerOrderDetail;
use App\Models\DealerProduct;
use App\Models\DealerWallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\Auth;

class DealerController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth:admin'])->except(['_stock_product']);
        
    }

    public function _store(DealerRequest $req)
    {
        try{
            $data=array('password'=>Hash::make($req->password), 'current_balance'=>$req->opening_balance,'user_id'=>date('Y').date('m').date('d').Dealer::all()->count());
            $new_dealer = Dealer::create(array_merge($req->except('password'),$data));
            $images=array();
            if($req->hasFile('image') && $req->file('image')->isValid()){
                array_push($images,$req->file('image'));
            }
            // dd($images);
            event(new UploadImageEvent($new_dealer,$images,'image'));
            if($new_dealer){
                return $this->success($new_dealer);
            }
            else{
                return $this->failed(null, 'New supplier creation failed', 500);
            }
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }


    //all supplier list fetch
    public function _all():JsonResponse
    {
        try{
            return $this->success(Dealer::all());
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }


    // update supplier details
    public function _update(Dealer $dealer,DealerRequest $req):JsonResponse
    {
        try{
            $data = [];
            if ($req->password) {
                $data=array('password'=>Hash::make($req->password));
            }
            $dealer->update(array_merge($req->except('password'),$data));
            return $this->success($dealer);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }


    //show specific suppler details
    public function _details(Dealer $dealer):JsonResponse
    {
        try{
            return $this->success(Dealer::with('roles')->with('wallet')->where('id',$dealer->id)->first());
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    public function _delete(Dealer $dealer): JsonResponse
    {
        $dealer->delete();
        return $this->success($dealer, 'Dealer Deleted Successfully');
    }

    public function _stock_product(Request $req):JsonResponse
    {
        try{
            $req->validate([
                // 'dealer_id'=>'required|exists:dealers,id',
                'products'=> 'required'
            ]);
            if(Auth::user()->wallet->product_balance > (float)$req->totalAmount){
                $new_order = DealerOrderDetail::create([
                    'dealer_id'=>Auth::user()->id,
                    'cus_first_name'=> $req->first_name,
                    'cus_last_name'=> $req->last_name,
                    'cus_email'=> $req->email,
                    'cus_phone'=> $req->phone,
                    'cus_address' => $req->delivery_address,
                    'total_amount' => $req->totalAmount,
                    'order_notes'=> $req->order_notes
                ]);
                foreach($req->products as $product){
                    DealerProduct::create([
                        'order_id'=>$new_order['id'],
                        'product_id'=> $product['product_id'],
                        'qty'=> $product['quantity'],
                    ]);
                }
                DealerWallet::where('dealer_id',Auth::user()->id)->update([
                    'product_balance'=> Auth::user()->wallet->product_balance-(float)$req->totalAmount
                ]);
                return $this->success($req->all());
            }
            else{
                return $this->failed(null,'Insuficient product balance');
            }
            
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }

    public function _update_product_balance(Request $req):JsonResponse
    {
        $req->validate([
            'dealer_id'=>'required|exists:dealers,id',
            'amount'=> 'required|numeric'
        ]);
        try{
            DB::table('dealer_wallets')->updateOrInsert(
                ['dealer_id' => $req->dealer_id],
                ['product_balance' => $req->amount]
            );
            return $this->success(DealerWallet::with('dealer')->where('dealer_id',$req->dealer_id)->first(), 'Dealer wallet updated successfully');
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
}
