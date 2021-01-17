<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\UnspecifiedModel;
use App\Models\Comment;
use App\Repositories\Contracts\IBase;
use App\Repositories\Contracts\IComment;
use App\Repositories\Criteria\ICriteria;
use Arr;
use Exception;

class CommentRepository  extends BaseRepository implements
    IComment
{
    public function model()
    {
        return Comment::class;
    }
}
