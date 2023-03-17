<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Request;
use Exception;

class EmployeeController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        $employees = Employee::where('company_id', $user->company_id)->with(['company'])->get();
        // return $employees;
        return EmployeeResource::collection($employees);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
        ]);
        try {
            $authUser = auth()->user();
            $data['company_id'] =  $authUser->company_id;
            $data['creator_id'] = $authUser->id;
            $data['phone'] = $request->phone;
            $employee = Employee::create($data);

            return response()->json(['message' => 'employee was created Successfully!'], 200);
        } catch (Exception  $Exception) {
            $employee->delete();
            throw new Exception('something went wrong andEmployee couldn\'t be created');
        }
    }


    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);
        if ($employee) {
            $data = $request->validate([
                'name' => 'required',
            ]);
            try {
                $data['phone'] = $request->phone;
                $employee->update($data);

                return response()->json(['message' => 'employee was updated Successfully!'], 200);
            } catch (Exception  $Exception) {
                throw new Exception('something went wrong andEmployee couldn\'t be updated');
            }
        }
        return response()->json(['message' => 'employee not found'], 404);
    }


    public function destroy($id)
    {
        $employee = Employee::find($id);
        if ($employee) {
            try {
                $employee->delete();

                return response()->json(['message' => 'employee was deleted Successfully!'], 200);
            } catch (Exception  $Exception) {
                throw new Exception('something went wrong andEmployee couldn\'t be deleted');
            }
        }
        return response()->json(['message' => 'employee not found'], 404);
    }
}
