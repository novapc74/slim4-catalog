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
            ->where('slug', $this->args)
            ->select('id', 'title', 'slug')
            ->first()
            ->toArray();

        $data = [
            'category' => $category,
            'memory' => self::humanizeUsageMemory(),
        ];

        return $this->jsonResponse($this->response, $data);
    }
}
