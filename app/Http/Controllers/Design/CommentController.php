<?php

namespace App\Http\Controllers\Design;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Repositories\Contracts\IComment;
use App\Repositories\Contracts\IDesign;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $comments;
    protected $designs;

    public function __construct(IComment $comments, IDesign $designs)
    {
        $this->comments = $comments;
        $this->designs = $designs;
    }

    public function store(Request $request, $designId)
    {


        $this->validate($request, [
            'body' => ['required'],
        ]);

        $comment = $this->designs->addComment($designId, [
            'body' => $request->body,
            'user_id' => auth()->id(),
        ]);

        return new CommentResource($comment);
    }

    public function update(Request $request, $designId)
    {

        $comment = $this->comments->find($designId);
        $this->authorize('update', $comment);

        $this->validate($request, [
            'body' => ['required'],
        ]);

        $comment = $this->comments->update($designId, [
            'body' => $request->body,
        ]);

        return new CommentResource($comment);
    }

    public function destroy(Request $request, $designId)
    {
        $comment = $this->comments->find($designId);
        $this->authorize('delete', $comment);

        $this->comments->delete($designId);

        return response()->json([
            "message" => "Comment deleted"
        ]);
    }
}
