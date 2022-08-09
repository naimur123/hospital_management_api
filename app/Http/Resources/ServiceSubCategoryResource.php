<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
// use App\Http\Resources\ServiceCategoryResource;

class ServiceSubCategoryResource extends JsonResource
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
    protected function filterFields($data){
        return collect($data)->forget($this->withoutFields)->toArray();
    }

    /**
     * Collection
     */
    public static function collection($resource){
        return tap(new ServiceSubCategoryCollection($resource), function ($collection) {
            $collection->collects = __CLASS__;
        });
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
            "service_category"          => (new ServiceCategoryResource($this->category))->hide(["created_by", "updated_by"]),
            "created_by"                => $this->created_by ? (new AdminResource($this->createdBy)) : null,
            "updated_by"                => $this->updated_by ? (new AdminResource($this->updatedBy)) : null,
        ]);
    }
}
