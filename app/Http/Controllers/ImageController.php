<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManagerStatic as Image;

//use App\Models\Image;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;


class ImageController extends Controller
{


    public function download($width, $height, $fileName)
    {
        $path = storage_path('public/storage/images/' . $fileName);
        !file_exists($path) && abort(403);

        $image = \Intervention\Image\ImageManagerStatic::make($path);
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

        $resized_filename = $original_image . '_' . $width . 'x' . $height . '.' . $fileName;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreImageRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreImageRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Image $image
     * @return \Illuminate\Http\Response
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Image $image
     * @return \Illuminate\Http\Response
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateImageRequest $request
     * @param \App\Models\Image $image
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateImageRequest $request, Image $image)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Image $image
     * @return \Illuminate\Http\Response
     */
    public function destroy(Image $image)
    {
        //
    }
}
