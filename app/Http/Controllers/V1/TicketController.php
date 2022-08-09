<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
Use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Http\Resources\TicketResource;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $this->data = TicketResource::collection(Ticket::all());
            $this->apiSuccess("Ticket Loaded Successfully");
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
                'patient_id' => 'required',
                'therapist_id' => 'required'    
            ]);
            
            if ($validator->fails()) {
    
                $this->apiOutput($this->getValidationError($validator), 200);
            }
   
            $ticket = new Ticket();
            $ticket->patient_id = $request->patient_id;
            $ticket->therapist_id = $request->therapist_id;
            $ticket->ticket_department_id = $request->ticket_department_id;
            $ticket->location = $request->location ?? null;
            $ticket->language = $request->language ?? null;
            $ticket->date = /*$request->date*/ Carbon::now();
            $ticket->strike = $request->strike;
            $ticket->strike_history = $request->strike_history ?? null;
            $ticket->ticket_history = $request->ticket_history ?? null;
            $ticket->remarks = $request->remarks ?? null;
            $ticket->status = $request->status;
            $ticket->created_by = $request->user()->id ?? null;
            // $ticket->created_at = Carbon::Now();
            $ticket->save();
            $this->apiSuccess();
            $this->data = (new TicketResource($ticket));
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
                'patient_id' => 'required',
                'therapist_id' => 'required' 
    
            ]
           );
            
           if ($validator->fails()) {
    
            $this->apiOutput($this->getValidationError($validator), 200);
           }
   
            $ticket = Ticket::find($id);
            $ticket->patient_id = $request->patient_id;
            $ticket->therapist_id = $request->therapist_id;
            $ticket->ticket_department_id = $request->ticket_department_id;
            $ticket->location = $request->location ?? null;
            $ticket->language = $request->language ?? null;
            $ticket->date = /*$request->date*/ Carbon::now();
            $ticket->strike = $request->strike;
            $ticket->strike_history = $request->strike_history ?? null;
            $ticket->ticket_history = $request->ticket_history ?? null;
            $ticket->remarks = $request->remarks ?? null;
            $ticket->status = $request->status;
            $ticket->updated_by = $request->user()->id ?? null;
            // $ticket->updated_at = Carbon::Now();
            $ticket->save();
            $this->apiSuccess();
            $this->data = (new TicketResource($ticket));
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
        Ticket::destroy($id);
        $this->apiSuccess();
        return $this->apiOutput("Ticket Deleted Successfully", 200);
    }
}
