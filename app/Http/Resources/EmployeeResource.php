<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'employeeName' => $this->name,
            'employeePhone' => $this->phone,
            'employeeCompany' => [
                'companyId' => $this->company->id,
                'companyName' => $this->company->company_name,
            ],
        ];
    }
}
