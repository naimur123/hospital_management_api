<?php


namespace App\Http\Controllers\V1;


use App\Http\Controllers\Controller;
use App\Models\Admin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\RequestGuard;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    /**
     * Show Login
     */
    public function showLogin(Request $request){
        $this->data = [
            "email"     => "required",
            "password"  => "required",
        ];
        $this->apiSuccess("This credentials are required for Login ");
        return $this->apiOutput(200);
    }

    /**
     * Login
     */
    public function login(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                "email"     => ["required", "email", "exists:admins,email"],
                "password"  => ["required", "string", "min:4", "max:40"]
            ]); 
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }
            $admin = Admin::where("email", $request->email)->first();
            if( !Hash::check($request->password, $admin->password) ){
                return $this->apiOutput("Sorry! Password Dosen't Match", 401);
            }
            if( !$admin->status ){
                return $this->apiOutput("Sorry! your account is temporaly blocked", 401);
            }
            // Issueing Access Token
            $this->access_token = $admin->createToken($request->ip() ?? "admin_access_token")->plainTextToken;
            Session::put('access_token',$this->access_token);
            // echo Session::get('access_token');
            $this->apiSuccess("Login Successfully");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }
    public function logout(Request $request){
        
        // Session::flush('access_token');
        // // $user = $request->user();
        // // $request->user()->access_token->delete();
        // $this->apiSuccess("Logout Successfull");
        // return $this->apiOutput();
        $user = auth('sanctum')->user();
        // 
        foreach ($user->tokens as $token) {
            $token->delete();
       }
       $this->apiSuccess("Logout Successfull");
       return $this->apiOutput();
   
    }
}
