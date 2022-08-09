<?php

namespace App\Http\Controllers\V1\Therapist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Therapist;
use App\Models\TherapistUpload;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Exception;
use App\Http\Resources\TherapistResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TherapistController extends Controller
{
    /**
     * Get Current Table Model
     */
    private function getModel(){
        return new Therapist();
    }

    /**
     * Show Login
     */
    public function showLogin(Request $request){
        $this->data = [
            "email"     => "required",
            "password"  => "required",
        ];
        $this->apiSuccess("This credentials are required for Login ");
        return $this->apiOutput();
    }

    /**
     * Login
     */
    public function login(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                "email"     => ["required", "email", "exists:therapists,email"],
                "password"  => ["required", "string", "min:4", "max:40"]
            ]); 
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }
            $therapist = $this->getModel()->where("email", $request->email)->first();
            if( !Hash::check($request->password, $therapist->password) ){
                return $this->apiOutput("Sorry! Password Dosen't Match", 401);
            }
            if( !$therapist->status ){
                return $this->apiOutput("Sorry! your account is temporaly blocked", 401);
            }
            // Issueing Access Token
            $this->access_token = $therapist->createToken($request->ip() ?? "therapist_access_token")->plainTextToken;
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $this->data = TherapistResource::collection(Therapist::all());
            $this->apiSuccess("Therapist Loaded Successfully");
            // return $this->apiOutput("Therapist Loaded Successfully",200);
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $validator = Validator::make(
                $request->all(),
                [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    "email"     => ["required", "email", "unique:therapists"],
                    "phone"     => ["required", "numeric", "unique:therapists"]
                ]
               );
            
           if ($validator->fails()) {
            return $this->apiOutput($this->getValidationError($validator), 400);
           }


            try{

                    DB::beginTransaction();
                    
                    $data = $this->getModel();
                    $data->created_by = $request->user()->id;

                    $data->first_name = $request->first_name;                  
                    $data->last_name = $request->last_name;         
                    $data->email = $request->email;
                    $data->phone = $request->phone;
                    $data->address = $request->address;
                    $data->language = $request->language;
                    $data->bsn_number = $request->bsn_number;
                    $data->dob_number = $request->dob_number;
                    $data->insurance_number = $request->insurance_number;
                    $data->emergency_contact = $request->emergency_contact ?? 0;
                    $data->gender = $request->gender;
                    $data->date_of_birth = /*$request->date_of_birth*/ Carbon::now();
                    $data->status = $request->status;
                    $data->therapist_type_id = $request->therapist_type_id;
                    $data->blood_group_id = $request->blood_group_id;
                    $data->state_id = $request->state_id;
                    $data->country_id = $request->country_id;
                    $data->password = bcrypt($request->password);
                    
                    $data->save();
                    $this->saveFileInfo($request, $data);
            
            DB::commit();
                try{
                    // event(new Registered($data));
                }catch(Exception $e){
                    //
                }
            }
            catch(Exception $e){
                return $this->apiOutput($this->getError( $e), 500);
                DB::rollBack();
            }
            $this->apiSuccess("Therapist Info Added Successfully");
            $this->data = (new TherapistResource($data));
            return $this->apiOutput();        
            }
            catch(Exception $e){
            
            return $this->apiOutput($this->getError( $e), 500);
        };
            
    }

    // Save File Info
    public function saveFileInfo($request, $therapist){
        $file_path = $this->uploadImage($request, 'file', $this->therapist_uploads, 720);
  
        if( !is_array($file_path) ){
            $file_path = (array) $file_path;
        }
        foreach($file_path as $path){
            $data = new TherapistUpload();
            $data->therapist_id = $therapist->id;
            $data->file_name    = $request->file_name ?? "Therapist Upload";
            $data->file_url     = $path;
            $data->save();
        }
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $validator = Validator::make(
                $request->all(),
                [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    "email"     => ["required", "email", /*"unique:therapists"*/Rule::unique('therapists', 'email')->ignore($request->id)],
                    "phone"     => ["required", "numeric",/* "unique:therapists"*/ Rule::unique('therapists', 'phone')->ignore($request->id)]
                ]
               );
            
           if ($validator->fails()) {
            return $this->apiOutput($this->getValidationError($validator), 400);
           }


            try{

                    DB::beginTransaction();
                    
                    $data = $this->getModel()->find($request->id);
                    $data->updated_by = $request->user()->id;

                    $data->first_name = $request->first_name;                  
                    $data->last_name = $request->last_name;         
                    $data->email = $request->email;
                    $data->phone = $request->phone;
                    $data->address = $request->address;
                    $data->language = $request->language;
                    $data->bsn_number = $request->bsn_number;
                    $data->dob_number = $request->dob_number;
                    $data->insurance_number = $request->insurance_number;
                    $data->emergency_contact = $request->emergency_contact ?? 0;
                    $data->gender = $request->gender;
                    $data->date_of_birth = /*$request->date_of_birth*/ Carbon::now();
                    $data->status = $request->status;
                    $data->therapist_type_id = $request->therapist_type_id;
                    $data->blood_group_id = $request->blood_group_id;
                    $data->state_id = $request->state_id;
                    $data->country_id = $request->country_id;
                    $data->password = bcrypt($request->password);
                    
                    $data->save();
                    $this->updateFileInfo($request, $data);
            
                    DB::commit();
                        try{
                            // event(new Registered($data));
                        }catch(Exception $e){
                            //
                        }
            }
            catch(Exception $e){
                return $this->apiOutput($this->getError( $e), 500);
                DB::rollBack();
            }
            $this->apiSuccess("Therapist Info Updated Successfully");
            $this->data = (new TherapistResource($data));
            return $this->apiOutput();        
            }
            catch(Exception $e){
            
            return $this->apiOutput($this->getError( $e), 500);
        };
    }

     //Update File Info
     public function updateFileInfo($request, $therapist){
    
        $data = TherapistUpload::find($request->ids);
        $data->therapist_id = $therapist->id;
        $data->file_name    = $request->file_name ?? "Therapist Upload Updated";
        $data->file_url     = $this->uploadImage($request, 'file', $this->therapist_uploads,null,null,$data->file_url);
        $data->save();

    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $data = $this->getModel()->find($id);
            TherapistUpload::where('therapist_id',$data->id)->delete();
            $data->delete();
            $this->apiSuccess();
            return $this->apiOutput("Therapist Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
}
