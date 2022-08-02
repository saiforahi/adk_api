<?php

namespace App\Http\Controllers\v1\sub_pre_dealers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubAndPreController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin'])->except([]);
        // $this->middleware('role:super-admin|unit-admin')->except([]);
    }
    
    public function _store(DealerRequest $req)
    {
        try{
            $data=array('password'=>Hash::make($req->password), 'user_id'=>date('Y').date('m').date('d').Dealer::all()->count());
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
            // dd($req->all());
            $data=array('password'=>Hash::make($req->password), 'user_id'=>date('Y').date('m').date('d').date('i').date('s'));
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
            return $this->success($dealer);
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
}
