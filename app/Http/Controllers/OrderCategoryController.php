<?php

namespace App\Http\Controllers;

use App\Models\OrderCategory;
use Exception;
use Illuminate\Http\Request;

class OrderCategoryController extends Controller
{

    public function index()
    {
        $category = OrderCategory::where('parent_id', null)->get();
        return response()->json(['order_category' => $category], 200);
    }
    public function subcategory()
    {
        $category = OrderCategory::where('parent_id', '!=', null)->get();
        return response()->json(['sub_category' => $category], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'order_name' => 'required'
        ]);
        try {
            OrderCategory::create($data);
            return response()->json(['message' => 'Order Category was created Successfully!'], 200);
        } catch (Exception $th) {
            throw new Exception('something went wrong and Order Category couldn\'t be created');
        }
    }

    public function storeSubcategory(Request $request)
    {
        $data = $request->validate([
            'order_name' => 'required|array',
            'category' => 'required|exists:order_categories,id'
        ]);
        $category = OrderCategory::find($request->category);

        if ($category) {
            try {
                foreach ($request->order_name as $cat) {

                    OrderCategory::create([
                        'order_name' => $cat,
                        'parent_id' => $category->id
                    ]);
                }
                return response()->json(['message' => 'Subcategory was created Successfully!'], 200);
            } catch (Exception $th) {
                throw new Exception('something went wrong and Subcategory couldn\'t be created');
            }
        }
    }
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'order_name' => 'required'
        ]);
        $category = OrderCategory::find($id);
        if ($category) {
            try {
                $category->update($data);
                return response()->json(['message' => 'category was updated Successfully!'], 200);
            } catch (Exception $th) {
                //throw $th;
                throw new Exception('something went wrong and category couldn\'t be updated');
            }
        }
    }

    public function destroy($id)
    {
        $category = OrderCategory::find($id);
        if ($category) {
            $category->delete();
            return response()->json(['message' => 'category was deleted Successfully!'], 200);
        }
        return response()->json(['message' => 'category not found'], 404);
    }
}
