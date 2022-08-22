<?php

namespace App\Http\Controllers\v1\TycoonPanel;

use App\Events\v1\UploadImageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\TycoonPanel\TycoonRequest;
use App\Models\Tycoon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;

class TycoonController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:tycoon'])->except([]);
    }
   
    //all tycoon list fetch
    public function _all():JsonResponse
    {
        try{
            return $this->success(Tycoon::where('reference_id', auth()->user()->id)->select('tycoons.*', DB::raw('CONCAT(tycoons.first_name, " ", tycoons.last_name) AS name'
            ))->get());
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    // tycoon store
    public function _store(TycoonRequest $req)
    {
        try{
            $tycoon='';
           
     
            $data=array('password'=>Hash::make($req->password), 'user_id'=>date('Y').date('m').date('d').Tycoon::all()->count(), 'reference_id' => auth()->user()->id);
            $tycoon = Tycoon::create(array_merge($req->except('password'),$data));
          
            $images=array();
            if($req->hasFile('image') && $req->file('image')->isValid()){
                array_push($images,$req->file('image'));
            }
            
            event(new UploadImageEvent($tycoon,$images,'image'));
            if($tycoon){
                return $this->success($tycoon);
            }
            else{
                return $this->failed(null, 'Creation failed', 500);
            }
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }

    // update tycoon details
    public function _update($pre_or_sub_dealer_id,TycoonRequest $req):JsonResponse
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
