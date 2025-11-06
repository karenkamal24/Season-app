<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemCategory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Helpers\LangHelper;

class ItemService
{
    /**
     * Get all active item categories
     */
    public function getAllCategories()
    {
        return ItemCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get items by category ID
     */
    public function getItemsByCategory(int $categoryId)
    {
        $category = ItemCategory::find($categoryId);

        if (!$category) {
            throw new NotFoundHttpException(LangHelper::msg('category_not_found'));
        }

        return Item::where('category_id', $category->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with('category')
            ->get();
    }

    /**
     * Get single item by ID
     */
    public function getItemById(int $itemId)
    {
        $item = Item::with('category')
            ->where('is_active', true)
            ->find($itemId);

        if (!$item) {
            throw new NotFoundHttpException(LangHelper::msg('item_not_found'));
        }

        return $item;
    }
}

