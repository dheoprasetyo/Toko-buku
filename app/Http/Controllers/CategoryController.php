<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filterKeyword = $request->get('name');

        $categories = \App\Category::paginate(10);

        if($filterKeyword){
            $categories = \App\Category::where("name", "LIKE", "%$filterKeyword%")->paginate(10);
            }

        return view('categories.index', ['categories' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name = $request->get('name');
        $new_category = new \App\Category;
        $new_category->name = $name;

        if($request->file('image')){
            $image_path = $request->file('image')->store('category_images', 'public');
            $new_category->image = $image_path;
        }
        $new_category->created_by = \Auth::user()->id;
        $new_category->slug = str_slug($name, '-');
        $new_category->save();

        return redirect()->route('categories.create')->with('status', 'Category successfully created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = \App\Category::findOrFail($id);
        return view('categories.show', ['category' => $category]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category_to_edit = \App\Category::findOrFail($id);
        return view('categories.edit', ['category' => $category_to_edit]);  
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $name = $request->get('name');
        $slug = $request->get('slug');
        $category = \App\Category::findOrFail($id);
        $category->name = $name;
        $category->slug = $slug;

        if($request->file('image')){
            if($category->image && file_exists(storage_path('app/public/' . $category->image))){
                \Storage::delete('public/' . $category->name);
            }
                $new_image = $request->file('image')->store('category_images','public');
                $category->image = $new_image;
        }
        $category->updated_by = \Auth::user()->id;
        // mengupdate slug berdasarkan nama baru category yang disubmit dari form edit,
        $category->slug = str_slug($name);
        $category->save();
        return redirect()->route('categories.edit', ['id' => $id])->with('status', 'Category successfully edited');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = \App\Category::findOrFail($id);
        $category->delete();

        return redirect()->route('categories.index')->with('status', 'Category successfully moved to trash');
    }

    // menampilkan daftar Category yang sedang soft delete
    public function trash(){
        $deleted_category = \App\Category::onlyTrashed()->paginate(10);
        return view('categories.trash', ['categories' => $deleted_category]);
    //         Kode tersebut melakukan query ke database categories menggunakan model Category dan kita
    // gunakan method onlyTrashed() untuk mendapatkan hanya kategori yang status nya soft delete yaitu yang
    // field deleted_at nya tidak NULL.
    // Setelah itu kita lempar sebagai variable $categories ke view categories.index.
    }

    public function restore($id){
        $category = \App\Category::withTrashed()->findOrFail($id);
        if($category->trashed()){
            $category->restore();
        } else {
            return redirect()->route('categories.index')->with('status', 'Category is not in trash');
        }
            return redirect()->route('categories.index')->with('status', 'Category successfully restored');
    }

    public function deletePermanent($id){
        $category = \App\Category::withTrashed()->findOrFail($id);
        if(!$category->trashed()){
            return redirect()->route('categories.index')->with('status', 'Can not delete permanent active category');
        } else {
            $category->forceDelete();
            return redirect()->route('categories.index')->with('status', 'Category permanently deleted');
        }
    }

    
    public function ajaxSearch(Request $request){
        $keyword = $request->get('q');
        $categories = \App\Category::where("name", "LIKE", "%$keyword%")->get();
        return $categories;
    }
}
