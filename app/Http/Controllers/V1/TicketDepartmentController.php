<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TicketDepartment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Http\Resources\TicketDepartmentResource;

class TicketDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $this->data = TicketDepartmentResource::collection(TicketDepartment::all());
            $this->apiSuccess("Ticket Department Type Loaded Successfully");
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
        $validator = Validator::make( $request->all(),[
                'name' => 'required|min:4',
                'remarks' => 'nullable|min:4'    
            ]);
            
            if ($validator->fails()) {
    
                $this->apiOutput($this->getValidationError($validator), 200);
            }
   
            $ticket_department = new TicketDepartment();
            $ticket_department->name = $request->name;
            $ticket_department->status = $request->status;
            $ticket_department->remarks = $request->remarks ?? "";
            $ticket_department->created_by = $request->user()->id ?? null;
            // $ticket_department->created_at = Carbon::Now();
            $ticket_department->save();
            $this->apiSuccess();
            $this->data = (new TicketDepartmentResource($ticket_department));
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
                'name' => 'required|min:4',
                'remarks' => 'nullable|min:4'
    
            ]
           );
            
           if ($validator->fails()) {
    
            $this->apiOutput($this->getValidationError($validator), 200);
           }
   
            $ticket_department = TicketDepartment::find($id);
            $ticket_department->name = $request->name;
            $ticket_department->status = $request->status;
            $ticket_department->remarks = $request->remarks ?? "";
            $ticket_department->updated_by = $request->user()->id ?? null;
            // $ticket_department->updated_at = Carbon::Now();
            $ticket_department->save();
            $this->apiSuccess();
            $this->data = (new TicketDepartmentResource($ticket_department));
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
        TicketDepartment::destroy($id);
        $this->apiSuccess();
        return $this->apiOutput("Ticket Department Deleted Successfully", 200);
    }
}
