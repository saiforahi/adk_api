<?php

namespace App\Http\Controllers\v1\auth;

use Auth;
use App\Models\User;
use App\Models\Dealer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        if (! $user || ! Hash::check($request->password, $user->password ) || !Auth::guard($user->guard__name())->attempt(['email' => $request->email, 'password' => $request->password])) {
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
            switch($request->type){
                case 'merchant':    //merchant creation
                    $validator=Validator::make($request->all(),$this->dealer_reg_rules);
                    if($validator->fails()){                                //merchant registration validation
                        return response()->json(['success'=>false,'errors'=>$validator->errors()], 422);
                    }
                    $user = User::create(array_merge($request->all(),['password' => bcrypt($request->password)]));
                    break;

                case 'courier':  //courier creation
                    $request->validate(['id_no'=>'required|unique:couriers,nid_no']);
                    $user = Courier::create(array_merge($request->all(),['employee_id'=>random_unique_string_generate(Courier::class,'employee_id'),'password' => bcrypt($request->password)]));
                    break;
            }
            if($request->id_type == 'NID'){
                $user->nid_no = $request->id_no;
            }
            else{
                $user->bin_no = $request->id_no;
            }
            $user->save();
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
}
