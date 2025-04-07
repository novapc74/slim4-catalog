<?php

namespace App\Action\Category;

use App\Models\Category;
use App\Action\AbstractAction;
use App\Traits\HumanSizeCounterTrait;
use Psr\Http\Message\ResponseInterface;

final class MainCategoryAction extends AbstractAction
{
    public function action(): ResponseInterface
    {
        $categories = Category::query()
            ->whereNull('parent_category_id')
            ->select('id', 'title', 'slug')
            ->get()
            ->toArray();

        $data = [
            'categories' => $categories,
            'memory' => self::humanizeUsageMemory(),
        ];

        return $this->jsonResponse($this->response, $data);
    }
}
