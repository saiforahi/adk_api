<?php

namespace App\Http\Controllers\v1\dealer;

use App\Events\v1\UploadImageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\DealerRequest;
use App\Models\Admin;
use App\Models\AdminWallet;
use App\Models\AdminStock;
use App\Models\BalanceTransfer;
use App\Models\Dealer;
use App\Models\DealerProduct;
use App\Models\DealerProductStock;
use App\Models\DealerWallet;
use App\Models\ProductStockOrder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DealerController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth:admin'])->except(['_product_stock_order','product_stocks']);
        
    }

    public function _store(DealerRequest $req)
    {

        if(Auth::user()->wallet && Auth::user()->wallet->product_balance < (float)$req->opening_balance){
            return $this->failed(null,'Insuficient product balance');
        }

        DB::beginTransaction();
        try{
            $data=array('password'=>Hash::make($req->password), 'current_balance'=>$req->opening_balance,'user_id'=>date('Y').date('m').date('d').Dealer::all()->count());
            $new_dealer = Dealer::create(array_merge($req->except('password'),$data));
            DealerWallet::create(
                [
                    'dealer_id' => $new_dealer->id,
                    'product_balance' => $req->opening_balance
                ],
            );

            AdminWallet::where('admin_id', auth()->user()->id)->update(
                ['product_balance' => DB::raw('product_balance-'. $req->opening_balance)]
            );

            // save transer history
            $new_transfer = BalanceTransfer::create([
                'amount'=> $req->opening_balance,
                'payment_type'=> 3,
                'status'=> 'APPROVED'
            ]);
            $new_transfer->transfer_from()->associate(Auth::user());
            $new_transfer->transfer_to()->associate(Dealer::find($new_dealer->id));
            $new_transfer->save();

            $images=array();
            if($req->hasFile('image') && $req->file('image')->isValid()){
                array_push($images,$req->file('image'));
            }
            // dd($images);
            event(new UploadImageEvent($new_dealer,$images,'image'));
            DB::commit();
            if($new_dealer){
                return $this->success($new_dealer);
            }
            else{
                return $this->failed(null, 'New supplier creation failed', 500);
            }
        }
        catch(Exception $e){
            DB::rollback();
            return $this->failed(null, $e->getMessage(), 500);
        }
    }


    //all supplier list fetch
    public function _all():JsonResponse
    {
        try{
            return $this->success(Dealer::with('wallet:id,dealer_id,product_balance')->get());
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

    public function _product_stock_order(Request $req):JsonResponse
    {
        DB::beginTransaction();
        try{
            $req->validate([
                // 'dealer_id'=>'required|exists:dealers,id',
                'products'=> 'required',
                'totalAmount'=>'required'
            ]);
            if(Auth::user()->wallet && Auth::user()->wallet->product_balance > (float)$req->totalAmount){
                foreach($req->products as $product){

                    $new_order = ProductStockOrder::create([
                        'order_id'=>"1",
                        'product_id'=>$product['product_id'],
                        'qty'=> $product['quantity'],
                        'price'=> $product['price'],
                        'order_notes'=> 'null'
                    ]);
                    if($product['from'] == 'adk'){
                        $admin_stock= AdminStock::where('product_id',$product['product_id'])->first();
                        if ($admin_stock->quantity < $product['quantity']) {
                            return $this->failed(null,'Not engounh quantity!');
                        }
                        $admin_stock->quantity-=floatval($product['quantity']);
                        $admin_stock->save();
                        if($admin_stock->quantity == 0){
                            $admin_stock->delete();
                        }

                        $new_order->order_from()->associate(Auth::user());
                        $new_order->order_to()->associate(Admin::first());
                        $new_order->save();
                    }
                    else{
                        $dealer_stock= DealerProductStock::where(['product_id'=>$product['product_id'],'dealer_id'=>$product['dealer_id']])->first();
                        if ($dealer_stock->qty < $product['quantity']) {
                            return $this->failed(null,'Not engounh quantity!');
                        }
                        $dealer_stock->qty-=floatval($product['quantity']);
                        $dealer_stock->save();
                        if($dealer_stock->quantity == 0){
                            $dealer_stock->delete();
                        }

                        $new_order->order_from()->associate(Auth::user());
                        $new_order->order_to()->associate(Dealer::find($product['dealer_id']));
                        $new_order->save();
                    }
                }
                DealerWallet::where('dealer_id',Auth::user()->id)->update([
                    'product_balance'=> Auth::user()->wallet->product_balance-(float)$req->totalAmount,
                ]);
                DB:: commit();

                return $this->success($req->all(), 'Product purchase successfully completed');
            }
            else{
                return $this->failed(null,'Insuficient product balance');
            }
            
        }
        catch(Exception $e){
            DB::rollback();
            return $this->failed(null, $e->getMessage(), 500);
        }
    }

    public function _update_product_balance(Request $req):JsonResponse
    {
        $req->validate([
            'dealer_id'=>'required',
            'amount'=> 'required|numeric'
        ]);

        if(Auth::user()->wallet && Auth::user()->wallet->product_balance < (float)$req->opening_balance){
            return $this->failed(null,'Insuficient product balance');
        }

        try{

            DealerWallet::updateOrInsert(
                ['dealer_id' => $req->dealer_id],
                ['product_balance' =>DB::raw('product_balance+'. $req->amount)]
            );
            AdminWallet::where('admin_id', auth()->user()->id)->update(
                ['product_balance' => DB::raw('product_balance-'. $req->amount)]
            );
            $new_transfer = BalanceTransfer::create([
                'amount'=> $req->amount,
                'payment_type'=> 1,
                'status'=> 'APPROVED'
            ]);

            $new_transfer->transfer_from()->associate(Auth::user());
            $new_transfer->transfer_to()->associate(Dealer::find($req->dealer_id));
            $new_transfer->save();

            return $this->success(DealerWallet::with('dealer')->where('dealer_id',$req->dealer_id)->first(), 'Dealer wallet updated successfully');
        }
        catch(Exception $e){
            DB::rollback();
            return $this->failed(null, $e->getMessage(), 500);
        }
    }

    public function product_stocks(){
        try{
            $stocks=DealerProductStock::leftJoin('products', 'dealer_product_stocks.product_id', 'products.id')
            ->select('dealer_product_stocks.*', 'products.name as product_name')
            ->where('dealer_id',Auth::user()->id)->orderBy('dealer_product_stocks.id', 'desc')->get();
            return $this->success($stocks, 'Dealer Product stock list',200);

        }
        catch (Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
}
