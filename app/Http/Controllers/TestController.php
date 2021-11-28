<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Http\Requests\StoreTestRequest;
use App\Http\Requests\UpdateTestRequest;
use Illuminate\Support\Facades\Storage;



class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tests = Test::orderBy('id', 'desc')->paginate(15);
        return view('index', compact('tests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreTestRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTestRequest $request)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        $path = $request->file('image')->store('public/images');
        $post = new Test();
        $post->title = $request->title;
        $post->image = $path;
        $post->save();

        return redirect()->route('tests.index')
            ->with('success', 'Test has been created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Test $test
     * @return \Illuminate\Http\Response
     */
    public function show(Test $test)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Test $test
     * @return \Illuminate\Http\Response
     */
    public function edit(Test $test)
    {
        return view('edit', compact('test'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateTestRequest $request
     * @param \App\Models\Test $test
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTestRequest $request, $id)
    {
        $request->validate([
            'title' => 'required',
        ]);

        $test = Test::find($id);
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);
            $path = $request->file('image')->store('public/storage/images');
            $test->image = $path;

            $test->title = $request->title;
            $test->save();

            return redirect()->route('tests.index')
                ->with('success', 'test updated successfully');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Test $test
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleteImage = Test::findOrFail($id);
        if (Storage::delete($deleteImage->image)) {
            $deleteImage->delete();
        }
        return redirect()->route('tests.index')
            ->with('success', 'test has been deleted successfully');
    }

//    public function download($width,$height , $image)
//    {
//        $path=storage_path('public/storage/images/'.$image);
//        dd($path);
//    }

}


