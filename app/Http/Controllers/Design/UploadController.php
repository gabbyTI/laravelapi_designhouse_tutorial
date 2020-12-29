<?php

namespace App\Http\Controllers\Design;

use App\Http\Controllers\Controller;
use App\Jobs\UploadImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UploadController extends Controller
{
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
        $design = auth()->user()->designs()->create([
            'image' => $filename,
            'disk' => config('site.upload_disk')
        ]);

        //dispatch a job to handle the image manipulation
        $this->dispatch(new UploadImage($design));

        return response()->json($design, 200);
    }
}
