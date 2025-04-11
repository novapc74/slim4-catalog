<?php

namespace App\Action\Category;

use App\Models\Category;
use App\Action\AbstractAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;

final class MainCategoryAction extends AbstractAction
{
    /**
     * @throws InvalidArgumentException
     */
    public function action(): ResponseInterface
    {
        $data =  $this->cache->get('main_categories', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            $item->tag('main_category');

            $categories = Category::query()
                ->whereNull('parent_category_id')
                ->select('id', 'title', 'slug')
                ->get()
                ->toArray();

            $data =  [
                'categories' => $categories ?? [],
                'memory' => self::humanizeUsageMemory()

            ];

            return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
        });


        return $this->jsonResponse($this->response, $data);
    }
}
