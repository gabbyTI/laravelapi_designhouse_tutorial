<?php

namespace App\Repositories\Contracts;

interface IDesign
{
    public function applyTags($id, array $data);
    public function removeTags($id);
    public function addComment($designId, array $data);
    public function like($id);
    public function isLikedByUser($id);
}
