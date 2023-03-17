<?php

namespace App\Http\Controllers;

use App\Http\Resources\WorkersResource;
use App\Models\Worker;
use Exception;
use Illuminate\Http\Request;

class WorkerController extends Controller
{

    public function index()
    {
        $authUser = auth()->user();
        $workers = Worker::where('company_id', $authUser->company_id)->get();
        return WorkersResource::collection($workers);
    }

    public function store(Request $request)
    {
        $data = $this->dataVaildation($request);
        try {
            $authUser = auth()->user();
            $data['company_id'] = $authUser->company_id;
            $worker = Worker::create($data);
            return response()->json(['message' => 'worker was created Successfully!'], 200);
        } catch (Exception $Exception) {
            throw new Exception('something went wrong andworker couldn\'t be created');
        }
    }

    public function update(Request $request, $id)
    {
        $data = $this->dataVaildation($request);
        $worker = Worker::find($id);
        try {
            $worker->update($data);
            return response()->json(['message' => 'worker was updated Successfully!'], 200);
        } catch (Exception $Exception) {
            throw new Exception('something went wrong andworker couldn\'t be updated');
        }
    }

    public function destroy($id)
    {
        $worker = Worker::find($id);
        if ($worker) {
            $worker->delete();
            return response()->json(['message' => 'worker was deleted Successfully!'], 200);
        }
        return response()->json(['message' => 'worker not found'], 404);
    }
    public function dataVaildation(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'id_number' => 'required|min:15',
            'supplier' => 'required|boolean',
            'supplier_id' => 'required_if:supplier,true|exists:suppliers,id'
        ]);
        return $data;
    }
}
