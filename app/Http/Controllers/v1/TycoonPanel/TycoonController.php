<?php

namespace App\Http\Controllers\v1\TycoonPanel;

use App\Events\v1\UploadImageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\TycoonPanel\TycoonRequest;
use App\Http\Requests\WalletTopUpRequest;
use App\Models\TopupRequest;
use App\Models\Tycoon;
use App\Models\TycoonWallet;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            return $this->success(Tycoon::where('reference_id', auth()->user()->id)->orWhere('reference_id', auth()->user()->reference_id)->select('tycoons.*', DB::raw('CONCAT(tycoons.first_name, " ", tycoons.last_name) AS name'
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
            $tycoon_wallet= TycoonWallet::create([
                'tycoon_id'=>$tycoon->id
            ]);
          
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

    // submit topup request
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
            return $this->failed($e->getMessage(), $e->getMessage(), 500);
        }
    }
}
