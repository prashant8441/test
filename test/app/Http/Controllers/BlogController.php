<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

use DataTables;
use Illuminate\Support\Facades\Auth;


        
class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   $user_role=Auth::user()->role;
        if($user_role == 'user'){
           $products =Blog::where('user_id',Auth::user()->id)->get();
             
        }
        else{
           $products =Blog::get();

        }
        if ($request->ajax()) {
         $user_role=Auth::user()->role;

            if($user_role == 'user'){
                $data =Blog::where('user_id',Auth::user()->id)->get();
                  
             }
             else{
                $data =Blog::get();
     
             }
     
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
   
                           $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct">Edit</a>';
   
                           $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">Delete</a>';
    
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
      
        return view('index',compact('products'));
    }
     
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)

    {   $request->validate([
            'title' =>'required',
            'description' =>'required',
            'start_date'      => 'required|date|before:end_date',
            'end_date'        => 'date|after:start_date',
       ]);
         $user_id=Auth::user()->id;
        $image = $request->file('image');
        $name = time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('/images');
        $image->move($destinationPath, $name);
       

        Blog::updateOrCreate(['id' => $request->blog_id],
                ['title' => $request->title, 'description' => $request->description,'is_active' => $request->is_active,'start_date' => $request->start_date,'end_date' => $request->end_date,'image'=>$name,'user_id'=>$user_id]);        
   
        return response()->json(['success'=>'Blog saved successfully.']);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Blog::find($id);
        return response()->json($product);
    }
  
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Blog::find($id)->delete();
     
        return response()->json(['success'=>'Blog deleted successfully.']);
    }
    public function welcome()
    {  $blogs=Blog::get();

        return view('welcome',compact('blogs'));
    }
}