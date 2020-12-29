<?php

namespace App\Http\Controllers\Design;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Models\Design;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    public function update(Request $request, $id)
    {

        $request->validate([
            'title' => ['required', 'unique:designs,title,' . $id],
            'description' => ['required', 'string', 'min:20', 'max:140'],
            'tags' => ['required']
        ]);

        $design = Design::findorfail($id);
        $this->authorize('update', $design);

        $design->update([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'is_live' => !$design->upload_successful ? false : $request->is_live
        ]);

        // apply tags
        $design->retag($request->tags);

        return new DesignResource($design);
    }

    public function destroy($id)
    {
        $design = Design::findorfail($id);
        $this->authorize('delete', $design);

        // delete files associated to the record
        foreach (['thumbnail', 'large', 'original'] as $size) {
            if (Storage::disk($design->disk)->exists("uploads/designs/{$size}/" . $design->image)) {
                Storage::disk($design->disk)->delete("uploads/designs/{$size}/" . $design->image);
            }
        }

        $design->detag();

        // delete record from db
        $design->delete();

        return response()->json([
            "message" => "Record deleted"
        ]);
    }
}
