<?php

namespace App\Http\Controllers\v1\auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $general_reg_rules=[
        'first_name' => 'required|string|between:2,100',
        'last_name' => 'required|string|between:2,100',
        'email' => 'required|string|email|max:100',
        "phone" => 'required|string|max:11|min:11',
        'phone' => ['required', 'regex:/(^(\+8801|8801|01|008801))[1|3-9]{1}(\d){8}$/','max:11','min:11'],
        'password' => 'required|string|min:8',
        'id_type'=> 'required|string',
        'id_no'=> 'required|string|max:13|min:10',
    ];
    protected $dealer_reg_rules=[
        'email' => 'unique:users',
        "phone" => 'unique:users',
        'shop_name' => 'sometimes|nullable',
        'address' => 'sometimes|nullable',
        'id_no'=> 'required|unique:users,nid_no,bin_no',
    ];
    public function __construct() {
        $this->middleware('auth:sanctum', ['except' => ['login','register']]);
    }
    public function validator($data,$rules){
        return Validator::make($data, $data);
    }
    public function logout(Request $request) {
        $request->user()->tokens()->delete(); //deleting all the tokens
        return response()->json(['success'=>true,'message' => 'User successfully signed out'],201);
    }

    public function user_details() {
        return response()->json(['success'=>true,'data'=>auth()->user()]);
    }

    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'errors'=>$validator->errors()], 422);
        }
        $user = get_first_user_by_email($request->email);
        if (! $user || ! Hash::check($request->password, $user->password )) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect'],
            ]);
        }
        $token=$user->createToken('api_token')->plainTextToken;
        $user['guard'] = $user->guard__name();
        return response()->json(['success'=>true,'token'=>$token,'message'=>'User Signed in!',"data"=>$user],200);
    }

    public function register(Request $request) {
        $validator=Validator::make($request->all(),$this->general_reg_rules);
        if($validator->fails()){                                            //validating general registration rules
            return response()->json(['success'=>false,'errors'=>$validator->errors()], 422);
        }

        try{
            $response='';
            $user = '';
           
            return response()->json([
                'success' => true,
                'message'=> 'Registration Successful',
                'data' => $user,
            ], 201);
        }
        catch (Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'User registration failed!',
            ], 500);
        }
    }
    public function change_password(Request $request) {
        $validator=Validator::make($request->all(),[
            'old_password'=>'required|string|min:8',
            'new_password'=>'required|string|min:8'
        ]);
        if($validator->fails()){                                            //validating general registration rules
            return response()->json(['success'=>false,'errors'=>$validator->errors()], 422);
        }

        try{
            $user = FacadesAuth::user();
            if(Hash::check($request->old_password,$user->password)){
                $user->password = Hash::make($request->new_password);
                $user->save();
            }
            else{
                return response()->json([
                    'success' => false,
                    'message'=> 'Wrong password',
                ], 200);
            }
           
            return response()->json([
                'success' => true,
                'message'=> 'Password changed Successfully',
                'data' => $user,
            ], 200);
        }
        catch (Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'User registration failed!',
            ], 500);
        }
    }
}
