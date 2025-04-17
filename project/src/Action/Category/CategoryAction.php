<?php

namespace App\Action\Category;

use App\Models\Category;
use App\Action\AbstractAction;
use Psr\Http\Message\ResponseInterface;

final class CategoryAction extends AbstractAction
{
    public function action(): ResponseInterface
    {
        $category = Category::query()
            ->where('slug', $this->args['slug'])
            ->select('id', 'title', 'slug')
            ->first()
            ->toArray();

        $data = [
            'category' => $category,
        ];

        return $this->jsonResponse($data);
    }
}
