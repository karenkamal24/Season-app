<?php

namespace App\Services;

use App\Models\TravelBag;
use App\Models\BagItem;
use App\Models\BagType;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Helpers\LangHelper;

class TravelBagService
{
    /**
     * Get or create travel bag for authenticated user by bag type
     */
    public function getOrCreateBagByType(int $bagTypeId = 1)
    {
        $user = Auth::user();
        $bagType = BagType::find($bagTypeId);

        if (!$bagType) {
            throw new NotFoundHttpException(LangHelper::msg('bag_type_not_found'));
        }

        $travelBag = TravelBag::firstOrCreate(
            [
                'user_id' => $user->id,
                'bag_type_id' => $bagType->id,
            ],
            [
                'max_weight' => $bagType->default_max_weight ?? 23.0,
                'weight_unit' => 'kg',
                'is_active' => true,
            ]
        );

        // Load bagItems - load item.category for regular items, and all items (including custom)
        $travelBag->load([
            'bagItems.item.category',
            'bagType'
        ]);

        return $travelBag;
    }

    /**
     * Get or create main travel bag for authenticated user
     */
    public function getOrCreateMainBag()
    {
        return $this->getOrCreateBagByType(1);
    }

    /**
     * Get travel bag for authenticated user by bag type
     */
    public function getBagByType(int $bagTypeId = 1)
    {
        $user = Auth::user();
        $bagType = BagType::find($bagTypeId);

        if (!$bagType) {
            throw new NotFoundHttpException(LangHelper::msg('bag_type_not_found'));
        }

        $travelBag = TravelBag::where('user_id', $user->id)
            ->where('bag_type_id', $bagType->id)
            ->first();

        if (!$travelBag) {
            throw new NotFoundHttpException(LangHelper::msg('travel_bag_not_found'));
        }

        // Load bagItems - load item.category for regular items, and all items (including custom)
        $travelBag->load([
            'bagItems.item.category',
            'bagType'
        ]);

        return $travelBag;
    }

    /**
     * Get main travel bag for authenticated user
     */
    public function getMainBag()
    {
        return $this->getBagByType(1);
    }

    /**
     * Update maximum weight
     */
    public function updateMaxWeight(float $maxWeight, ?string $weightUnit = null, int $bagTypeId = 1)
    {
        $travelBag = $this->getOrCreateBagByType($bagTypeId);

        $updateData = ['max_weight' => $maxWeight];
        if ($weightUnit) {
            $updateData['weight_unit'] = $weightUnit;
        }

        $travelBag->update($updateData);
        // Load bagItems - load item.category for regular items, and all items (including custom)
        $travelBag->load([
            'bagItems.item.category',
            'bagType'
        ]);

        return $travelBag;
    }

    /**
     * Add item to travel bag
     */
    public function addItem(?int $itemId = null, int $quantity = 1, ?float $customWeight = null, int $bagTypeId = 1, ?string $customItemName = null)
    {
        $travelBag = $this->getOrCreateBagByType($bagTypeId);

        // Either item_id or custom_item_name must be provided
        if (!$itemId && !$customItemName) {
            throw new \Exception(LangHelper::msg('item_id_or_name_required'));
        }

        // If custom item, weight is required
        if ($customItemName && $customWeight === null) {
            throw new \Exception(LangHelper::msg('custom_weight_required_for_custom_item'));
        }

        $item = null;
        $itemWeight = null;

        if ($itemId) {
            // Regular item from items table
            $item = Item::find($itemId);
            if (!$item) {
                throw new NotFoundHttpException(LangHelper::msg('item_not_found'));
            }
            $itemWeight = $customWeight ?? $item->default_weight;
        } else {
            // Custom item
            $itemWeight = $customWeight;
        }

        // Check if current weight already exceeds max weight
        $currentWeight = $travelBag->current_weight;
        if ($currentWeight >= $travelBag->max_weight) {
            throw new \Exception(LangHelper::msg('cannot_add_more_weight_exceeded'));
        }

        // Check if item already exists in bag
        $bagItem = null;
        if ($itemId) {
            // Check for regular item
            $bagItem = BagItem::where('travel_bag_id', $travelBag->id)
                ->where('item_id', $itemId)
                ->first();
        } else {
            // Check for custom item with same name
            $bagItem = BagItem::where('travel_bag_id', $travelBag->id)
                ->where('custom_item_name', $customItemName)
                ->whereNull('item_id')
                ->first();
        }

        // Calculate current weight (excluding this item if it exists)
        $currentWeight = $travelBag->current_weight;

        if ($bagItem) {
            // Remove existing item weight from current weight
            if ($itemId && $item) {
                // Regular item
                $existingItemWeight = $bagItem->custom_weight ?? $item->default_weight;
            } else {
                // Custom item - use stored custom_weight
                $existingItemWeight = $bagItem->custom_weight ?? $itemWeight;
            }
            $currentWeight -= ($existingItemWeight * $bagItem->quantity);

            // Calculate new total weight after adding quantity
            $newQuantity = $bagItem->quantity + $quantity;
            $newTotalWeight = $currentWeight + ($itemWeight * $newQuantity);
        } else {
            // Calculate new total weight for new item
            $newTotalWeight = $currentWeight + ($itemWeight * $quantity);
        }

        // Check if adding this item will exceed max weight
        if ($newTotalWeight > $travelBag->max_weight) {
            throw new \Exception(LangHelper::msg('cannot_add_more_weight_exceeded'));
        }

        if ($bagItem) {
            // Update quantity
            $bagItem->quantity += $quantity;
            if ($customWeight !== null || $customItemName) {
                $bagItem->custom_weight = $itemWeight;
            }
            if ($customItemName) {
                $bagItem->custom_item_name = $customItemName;
            }
            $bagItem->save();
        } else {
            // Create new bag item
            $bagItem = BagItem::create([
                'travel_bag_id' => $travelBag->id,
                'item_id' => $itemId,
                'custom_item_name' => $customItemName,
                'quantity' => $quantity,
                'custom_weight' => $itemWeight,
            ]);
        }

        // Load relationships - item.category will be null for custom items (which is fine)
        if ($itemId) {
            $bagItem->load('item.category');
        }
        $travelBag->load(['bagItems.item.category', 'bagType']);

        return [
            'bag_item' => $bagItem,
            'travel_bag' => $travelBag,
        ];
    }

    /**
     * Remove item from travel bag
     */
    public function removeItem(int $itemId, ?int $quantity = null, int $bagTypeId = 1)
    {
        $travelBag = $this->getBagByType($bagTypeId);

        $bagItem = BagItem::where('travel_bag_id', $travelBag->id)
            ->where('item_id', $itemId)
            ->first();

        if (!$bagItem) {
            throw new NotFoundHttpException(LangHelper::msg('bag_item_not_found'));
        }

        $quantityToRemove = $quantity ?? $bagItem->quantity;

        // Validate quantity to remove
        if ($quantityToRemove > $bagItem->quantity) {
            throw new \Exception(LangHelper::msg('quantity_exceeds_available'));
        }

        if ($quantityToRemove >= $bagItem->quantity) {
            // Remove entire item
            $bagItem->delete();
        } else {
            // Decrease quantity
            $bagItem->quantity -= $quantityToRemove;
            $bagItem->save();
        }

        // Load bagItems - load item.category for regular items, and all items (including custom)
        $travelBag->load([
            'bagItems.item.category',
            'bagType'
        ]);

        return $travelBag;
    }

    /**
     * Update item quantity in travel bag
     */
    public function updateItemQuantity(int $itemId, int $quantity, int $bagTypeId)
    {
        $travelBag = $this->getBagByType($bagTypeId);

        $bagItem = BagItem::where('travel_bag_id', $travelBag->id)
            ->where('item_id', $itemId)
            ->first();

        if (!$bagItem) {
            throw new NotFoundHttpException(LangHelper::msg('bag_item_not_found'));
        }

        // Calculate weight for the new quantity
        if ($bagItem->item_id && $bagItem->item) {
            $itemWeight = $bagItem->custom_weight ?? $bagItem->item->default_weight;
        } else {
            // Custom item - use stored custom_weight
            $itemWeight = $bagItem->custom_weight;
        }

        // Calculate current weight excluding this item
        $currentWeight = $travelBag->current_weight;
        $existingItemWeight = $itemWeight * $bagItem->quantity;
        $weightWithoutItem = $currentWeight - $existingItemWeight;

        // Calculate new total weight with updated quantity
        $newItemWeight = $itemWeight * $quantity;
        $newTotalWeight = $weightWithoutItem + $newItemWeight;

        // Check if new quantity will exceed max weight
        if ($newTotalWeight > $travelBag->max_weight) {
            throw new \Exception(LangHelper::msg('cannot_add_more_weight_exceeded'));
        }

        // Update quantity
        $bagItem->quantity = $quantity;
        $bagItem->save();

        // Load relationships - item.category will be null for custom items (which is fine)
        if ($bagItem->item_id) {
            $bagItem->load('item.category');
        }
        $travelBag->load(['bagItems.item.category', 'bagType']);

        return [
            'bag_item' => $bagItem,
            'travel_bag' => $travelBag,
        ];
    }

    /**
     * Get all items in travel bag by bag type
     */
    public function getBagItems(int $bagTypeId = 1)
    {
        $travelBag = TravelBag::where('user_id', Auth::id())
            ->where('bag_type_id', $bagTypeId)
            ->first();

        if (!$travelBag) {
            return null;
        }

        // Load bagItems - load item.category for regular items, and all items (including custom)
        $travelBag->load([
            'bagItems.item.category',
            'bagType'
        ]);

        return $travelBag;
    }

    /**
     * Get all travel bags for authenticated user
     */
    public function getAllUserBags()
    {
        $user = Auth::user();

        // Load bagItems - load item.category for regular items, and all items (including custom)
        $travelBags = TravelBag::where('user_id', $user->id)
            ->with([
                'bagItems.item.category',
                'bagType'
            ])
            ->get();

        return $travelBags;
    }
}


