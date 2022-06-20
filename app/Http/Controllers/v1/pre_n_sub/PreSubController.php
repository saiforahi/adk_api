<?php

namespace App\Http\Controllers\v1\pre_n_sub;

use App\Events\v1\UploadImageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\PreSubDealerRequest;
use App\Models\PreDealer;
use App\Models\SubDealer;
use App\Models\SubDealerTypes;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PreSubController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin'])->except([]);
        // $this->middleware('role:super-admin|unit-admin')->except([]);
    }
    public function create_sub_dealer(PreSubDealerRequest $req){
        try{
            
            $data=array('password'=>Hash::make($req->password), 'type'=>'sub','user_id'=>date('Y').date('m').date('d').SubDealer::all()->count());
            $new_sub_dealer = SubDealer::create(array_merge($req->except('password'),$data));
            return $new_sub_dealer;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
    public function create_pre_dealer(PreSubDealerRequest $req){
        try{
            // dd('working');
            $data=array('password'=>Hash::make($req->password), 'type'=>'pre', 'user_id'=>date('Y').date('m').date('d').PreDealer::all()->count());
            $new_pre_dealer = PreDealer::create(array_merge($req->except('password'),$data));
            return $new_pre_dealer;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
    public function _all_sub_dealer_types(){
        try{
            return $this->success(SubDealerTypes::all());
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    public function _store(PreSubDealerRequest $req)
    {
        try{
            $new_pre_or_sub_dealer='';
            if($req->opening_balance >= 2000){
                $data=array('password'=>Hash::make($req->password), 'type'=>'sub','user_id'=>date('Y').date('m').date('d').SubDealer::all()->count());
                $new_pre_or_sub_dealer = SubDealer::create(array_merge($req->except('password'),$data));
            }
            else{
                $data=array('password'=>Hash::make($req->password), 'type'=>'pre', 'user_id'=>date('Y').date('m').date('d').PreDealer::all()->count());
                $new_pre_or_sub_dealer = PreDealer::create(array_merge($req->except('password'),$data));
            }
            $images=array();
            if($req->hasFile('image') && $req->file('image')->isValid()){
                array_push($images,$req->file('image'));
            }
            
            event(new UploadImageEvent($new_pre_or_sub_dealer,$images,'image'));
            if($new_pre_or_sub_dealer){
                return $this->success($new_pre_or_sub_dealer);
            }
            else{
                return $this->failed(null, 'Creation failed', 500);
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
            return $this->success(PreDealer::all());
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }


    // update supplier details
    public function _update($pre_or_sub_dealer_id,PreSubDealerRequest $req):JsonResponse
    {
        try{
            // dd($req->all());
            $pre_or_sub_dealer=PreDealer::find($pre_or_sub_dealer_id);
            $data=array('password'=>Hash::make($req->password), 'user_id'=>date('Y').date('m').date('d').date('i').date('s'));
            $pre_or_sub_dealer->update(array_merge($req->except('password'),$data));
            return $this->success($pre_or_sub_dealer);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }


    //show specific suppler details
    public function _details(PreDealer $dealer):JsonResponse
    {
        try{
            return $this->success($dealer);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    public function _delete(PreDealer $dealer): JsonResponse
    {
        $dealer->delete();
        return $this->success($dealer, 'Dealer Deleted Successfully');
    }
}
