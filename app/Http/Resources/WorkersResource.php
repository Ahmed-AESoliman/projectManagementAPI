<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'workerId' => $this->id,
            'workerName' => $this->name,
            'WorkerIdNumber' => $this->id_number,
            'workerPhone' => $this->phone,
            'workerAddress' => $this->address,
            'workerSuplier' => $this->supplier_id ? $this->supplier->name : $this->company->name,
            'WorkIn' => $this->company->name,
        ];
    }
}
