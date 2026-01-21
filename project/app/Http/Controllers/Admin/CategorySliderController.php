<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\CategorySlider;
use Illuminate\Http\Request;
use Validator;

class CategorySliderController extends AdminBaseController
{
    public function index()
    {
        $sliders = CategorySlider::with('category')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.category_sliders.index', compact('sliders'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.category_sliders.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'photo' => 'required|mimes:jpeg,jpg,png,svg',
            'category_id' => 'nullable',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->getMessageBag()->toArray()]);
        }

        $data = new CategorySlider();
        $input = $request->all();

        if ($file = $request->file('photo')) {
            $name = \PriceHelper::ImageCreateName($file);
            $file->move('assets/images/category-sliders', $name);
            $input['photo'] = $name;
        }

        $data->fill($input)->save();

        return response()->json(__('New Data Added Successfully.'));
    }

    public function edit($id)
    {
        $data = CategorySlider::findOrFail($id);
        $categories = Category::orderBy('name')->get();

        return view('admin.category_sliders.edit', compact('data', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'photo' => 'mimes:jpeg,jpg,png,svg',
            'category_id' => 'nullable',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->getMessageBag()->toArray()]);
        }

        $data = CategorySlider::findOrFail($id);
        $input = $request->all();

        if ($file = $request->file('photo')) {
            $name = \PriceHelper::ImageCreateName($file);
            $file->move('assets/images/category-sliders', $name);
            if (!empty($data->photo) && file_exists(public_path('assets/images/category-sliders/' . $data->photo))) {
                unlink(public_path('assets/images/category-sliders/' . $data->photo));
            }
            $input['photo'] = $name;
        }

        $data->update($input);

        return response()->json(__('Data Updated Successfully.'));
    }

    public function destroy($id)
    {
        $data = CategorySlider::findOrFail($id);

        if (!empty($data->photo) && file_exists(public_path('assets/images/category-sliders/' . $data->photo))) {
            unlink(public_path('assets/images/category-sliders/' . $data->photo));
        }

        $data->delete();

        return response()->json(__('Data Deleted Successfully.'));
    }

    public function status($id1, $id2)
    {
        $data = CategorySlider::findOrFail($id1);
        $data->status = $id2;
        $data->update();

        return response()->json(__('Status Updated Successfully.'));
    }
}
