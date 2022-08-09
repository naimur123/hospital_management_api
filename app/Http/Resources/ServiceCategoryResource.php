<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
// use App\Http\Resources\ServiceSubCategoryResource;

class ServiceCategoryResource extends JsonResource
{
    protected $withoutFields = [];

    /**
     * Set Hidden Item 
     */
    public function hide(array $hide = []){
        $this->withoutFields = $hide;
        return $this;
    }

    /**
     * Filter Hide Items
     */
    protected function filter($data){
        return collect($data)->forget($this->withoutFields)->toArray();
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->filter([
            "id"                       => $this->id,
            "name"                      => $this->name,
            "status"                    => $this->status,
            "remarks"                   => $this->remarks,
            "created_by"                => $this->created_by ? (new AdminResource($this->createdBy)) : null,
            "updated_by"                => $this->updated_by ? (new AdminResource($this->updatedBy)) : null,
        ]);
    }
}
