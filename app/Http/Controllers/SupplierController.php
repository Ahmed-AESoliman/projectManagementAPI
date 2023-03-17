<?php

namespace App\Http\Controllers;

use App\Http\Resources\SuppliersResource;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Exception;

class SupplierController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $suppliers = Supplier::where('company_id', $user->company_id)->with(['company'])->get();
        // return $suppliers;
        return SuppliersResource::collection($suppliers);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);
        try {
            $authUser = auth()->user();
            $data['company_id'] =  $authUser->company_id;
            $data['creator_id'] = $authUser->id;
            $supplier = supplier::create($data);

            return response()->json(['message' => 'supplier was created Successfully!'], 200);
        } catch (Exception  $Exception) {
            $supplier->delete();
            throw new Exception('something went wrong andsupplier couldn\'t be created');
        }
    }


    public function update(Request $request, $id)
    {
        $supplier = supplier::find($id);
        if ($supplier) {
            $data = $request->validate([
                'name' => 'required',
                'phone' => 'required',
                'address' => 'required',
            ]);
            try {
                $supplier->update($data);

                return response()->json(['message' => 'supplier was updated Successfully!'], 200);
            } catch (Exception  $Exception) {
                throw new Exception('something went wrong andsupplier couldn\'t be updated');
            }
        }
        return response()->json(['message' => 'supplier not found'], 404);
    }


    public function destroy($id)
    {
        $supplier = supplier::find($id);
        if ($supplier) {
            try {
                $supplier->delete();

                return response()->json(['message' => 'supplier was deleted Successfully!'], 200);
            } catch (Exception  $Exception) {
                throw new Exception('something went wrong andsupplier couldn\'t be deleted');
            }
        }
        return response()->json(['message' => 'supplier not found'], 404);
    }
}
