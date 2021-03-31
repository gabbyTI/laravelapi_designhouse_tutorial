<?php

namespace App\Http\Controllers\Design;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Jobs\UploadImage;
use App\Models\User;
use App\Repositories\Contracts\IDesign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UploadController extends Controller
{
    protected $designs;

    public function __construct(IDesign $designs)
    {
        $this->designs = $designs;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => ['required', 'mimes:png,jpeg,gif,bmp', 'max:2048']
        ]);

        //get the image
        $image = $request->file('image');
        $image_path = $image->getPathName();

        // orginal file name and replace spaces with _
        //Busiess Card = timestamp()_business_card
        $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));

        // move to temp storage (tmp)
        $tmp = $image->storeAs('uploads/original', $filename, 'tmp');


        //create the database record for the design
        // $design = auth()->user()->designs()->create([
        //     'image' => $filename,
        //     'disk' => config('site.upload_disk')
        // ]);

        $design = $this->designs->create([
            'user_id' => auth()->user()->id,
            'image' => $filename,
            'disk' => config('site.upload_disk')
        ]);

        //dispatch a job to handle the image manipulation
        UploadImage::dispatch($design);
        // $this->dispatch('App\Jobs\UploadImage', $design);
        // dd($tmp);

        return new DesignResource($design);
    }
}
