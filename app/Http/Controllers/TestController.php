<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Http\Requests\StoreTestRequest;
use App\Http\Requests\UpdateTestRequest;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic;



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
            'width'=>'required|int|min:50|max:300',
            'height'=>'required|int|min:50|max:300',
        ]);
        $post = new Test();
        $post->title = $request->title;
        $post->width= $request->width;
        $post->height = $request->height;



        $path = $request->file('image')->store('uploads2');


        $path = storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
//
//        dd($path);
        $img = Image::make($path);
            $img->resize($post->width,$post->height)->save();
        dd($img);

        $post->image = $img;

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

    protected function download($width, $height, $fileName)
    {
        $image_path = storage_path('public/storage/images/' . $fileName);
        !file_exists($image_path) && abort(403);

        $image = \Intervention\Image\ImageManagerStatic::make($image_path);
        $original_width = $image->width();
        $original_height = $image->height();

        $original_ratio = $original_width / $original_height;
        $request_ratio = $width / $height;

        if ($request_ratio < $original_ratio) {
            $new_width = (int)($original_height * $request_ratio);
            $image->resizeCanvas($new_width, null);
        } else {
            $new_height = (int)($original_width / $request_ratio);
            $image->resizeCanvas(null, $new_height);
        }

        $original_image = resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $resized_file_path = '';
        $resized_filename = $image . '_' . $width . 'x' . $height . '.' . 'png';
        $original_image->save($resized_file_path);
        !file_exists($resized_file_path) && abort(404);
        return response()->download($resized_file_path,null,[],null);

    }

//    public function download($width,$height , $image)
//    {
//        $path=storage_path('public/storage/images/'.$image);
//        dd($path);
//    }

}


