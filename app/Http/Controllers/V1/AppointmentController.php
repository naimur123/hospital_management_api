<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointmnet;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
     /**
     * Get Current Table Model
     */
    private function getModel(){
        return new Appointmnet();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $this->data = AppointmentResource::collection(Appointmnet::all());
            $this->apiSuccess("Appointment Loaded Successfully");
            // return $this->apiOutput("Therapist Loaded Successfully",200);
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //     $validator = Validator::make(
        //         $request->all(),
        //         [
        //             // 'first_name' => 'required',
        //             // 'last_name' => 'required',
        //             // "email"     => ["required", "email", "unique:therapists"],
        //             // "phone"     => ["required", "numeric", "unique:therapists"]
        //         ]
        //        );
            
        //    if ($validator->fails()) {
        //     return $this->apiOutput($this->getValidationError($validator), 400);
        //    }
        try{

            // $date  = $request->date;
            // $dformat = 'd/m/Y';
            // $time = $request->time;
            // $tformat = 'H:i:m';
            // Carbon\Carbon::createFromFormat($format, $input)
            // if($request->time < date('H:i:s')){
                // $request->time = date('H:i:s');
            // }


            DB::beginTransaction();
                    
            $data = $this->getModel();
            $data->created_by = $request->user()->id;

            $data->therapist_id = $request->therapist_id;
            $data->patient_id   = $request->patient_id;
            $data->therapist_schedule_id = $request->therapist_schedule_id;
            $data->number = $request->number;
            $data->history = $request->history ?? null;
            // $data->date = Carbon::createFromFormat($dformat, $date);
            // $data->time = Carbon::createFromFormat($tformat, $time);
            $data->date = Carbon::now();
            // $data->time = Carbon::now();
            // $data->date = $request->date;
            $data->time = $request->time;
            $data->fee = $request->fee;
            $data->language = $request->language;
            $data->type = $request->type;
            $data->therapist_comment = $request->comment ?? null;
            $data->remarks = $request->remarks ?? null;
            $data->status = $request->status;
            $data->save();

            DB::commit();
        
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
            DB::rollBack();
        }
        $this->apiSuccess("Appointment Created Successfully");
        $this->data = (new AppointmentResource($data));
        return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
            //     $validator = Validator::make(
        //         $request->all(),
        //         [
        //             // 'first_name' => 'required',
        //             // 'last_name' => 'required',
        //             // "email"     => ["required", "email", "unique:therapists"],
        //             // "phone"     => ["required", "numeric", "unique:therapists"]
        //         ]
        //        );
            
        //    if ($validator->fails()) {
        //     return $this->apiOutput($this->getValidationError($validator), 400);
        //    }
        try{
            DB::beginTransaction();
                    
            $data = $this->getModel()->find($id);
            $data->updated_by = $request->user()->id;

            $data->therapist_id = $request->therapist_id;
            $data->patient_id   = $request->patient_id;
            $data->therapist_schedule_id = $request->therapist_schedule_id;
            $data->number = $request->number;
            $data->history = $request->history ?? null;
            // $data->date = Carbon::createFromFormat($dformat, $date);
            // $data->time = Carbon::createFromFormat($tformat, $time);
            $data->date = Carbon::now();
            // $data->time = Carbon::now();
            // $data->date = $request->date;
            $data->time = $request->time;
            $data->fee = $request->fee;
            $data->language = $request->language;
            $data->type = $request->type;
            $data->therapist_comment = $request->comment ?? null;
            $data->remarks = $request->remarks ?? null;
            $data->status = $request->status;
            $data->save();

            DB::commit();

        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
            DB::rollBack();
        }
        $this->apiSuccess("Appointment Updated Successfully");
        $this->data = (new AppointmentResource($data));
        return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
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
            $data->delete();
            $this->apiSuccess();
            return $this->apiOutput("Appointment Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
}
