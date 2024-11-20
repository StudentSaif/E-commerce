<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Image;


class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::latest();

        if (!empty($request->get('keyword'))) {
            $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        $categories = $categories->paginate(6);
        // dd($categories);

        return view('admin.category.list', compact('categories'));
    }

    public function create()
    {
        // echo 'hello';
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        // echo 'hii';
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);

        if ($validator->passes()) {

            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            // Save images
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);

                if ($tempImage) { // Check if tempImage exists
                    $extension = explode('.', $tempImage->name);
                    $ext = last($extension);

                    $newImageName = $category->id . '.' . $ext;
                    $sPath = public_path() . '/temp/' . $tempImage->name;
                    $dPath = public_path() . '/uploads/category/' . $newImageName;

                    // Ensure the source file exists before copying
                    if (File::exists($sPath)) {
                        File::copy($sPath, $dPath);
                        $category->image = $newImageName;
                        $category->save();
                    } else {
                        Session::flash('error', 'Image file not found.');
                    }
                } else {
                    Session::flash('error', 'Temporary image not found.');
                }
            }

            // $request->session()->flash('success', 'Category added successfully');
            Session::flash('success', 'Category added successfully');

            return response()->json([
                'status' => true,
                'message' => 'Category added successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($categoryId, Request $request)
    {
        $category = Category::find($categoryId);
        if (empty($category)) {
            return redirect()->route('categories/index');
        }
        return view('admin.category.edit', compact('category'));
    }

    public function update(Request $request, $categoryId)
    {
        $category = Category::find($categoryId);
        if (empty($category)) {
            Session::flash('error', 'Category not found');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Category not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $category->id,
        ]);

        if ($validator->passes()) {
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            $oldImage = $category->image;

            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);

                if ($tempImage) {
                    $extension = explode('.', $tempImage->name);
                    $ext = last($extension);

                    $newImageName = $category->id . '-' . time() . '.' . $ext;
                    $sPath = public_path() . '/temp/' . $tempImage->name;
                    $dPath = public_path() . '/uploads/category/' . $newImageName;

                    if (File::exists($sPath)) {
                        File::copy($sPath, $dPath);
                        $category->image = $newImageName;
                        $category->save();

                        //deleting old image
                        File::delete(public_path() . '/uploads/category/' . $oldImage);
                    } else {
                        Session::flash('error', 'Image file not found.');
                    }
                } else {
                    Session::flash('error', 'Temporary image not found.');
                }
            }

            Session::flash('success', 'Category updated successfully');

            return response()->json([
                'status' => true,
                'message' => 'Category updated successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }


    public function destroy(Request $request, $categoryId)
    {
        $category = Category::find($categoryId);
        if (empty($category)) {
            Session::flash('error', 'Category not Found');
            return response()->json([
                'status' => true,
                'message' => 'Category not Found'
            ]);

            // return redirect()->route('categories/index');
        }

        File::delete(public_path() . '/uploads/category/' . $category->image);

        $category->delete();

        Session::flash('success', 'Category Deleted Successfully');

        return response()->json([
            'status' => true,
            'message' => 'Category Deleted Successfully'
        ]);
    }
}
