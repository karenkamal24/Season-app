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
     * Get or create main travel bag for authenticated user
     */
    public function getOrCreateMainBag()
    {
        $user = Auth::user();
        // Main cargo bag is always ID 1 (first bag type created)
        $bagType = BagType::find(1);

        if (!$bagType) {
            throw new NotFoundHttpException(LangHelper::msg('bag_type_not_found'));
        }

        $travelBag = TravelBag::firstOrCreate(
            [
                'user_id' => $user->id,
                'bag_type_id' => $bagType->id,
            ],
            [
                'max_weight' => 23.0,
                'weight_unit' => 'kg',
                'is_active' => true,
            ]
        );

        $travelBag->load(['bagItems.item.category', 'bagType']);

        return $travelBag;
    }

    /**
     * Get main travel bag for authenticated user
     */
    public function getMainBag()
    {
        $user = Auth::user();
        // Main cargo bag is always ID 1 (first bag type created)
        $bagType = BagType::find(1);

        if (!$bagType) {
            throw new NotFoundHttpException(LangHelper::msg('bag_type_not_found'));
        }

        $travelBag = TravelBag::where('user_id', $user->id)
            ->where('bag_type_id', $bagType->id)
            ->first();

        if (!$travelBag) {
            throw new NotFoundHttpException(LangHelper::msg('travel_bag_not_found'));
        }

        $travelBag->load(['bagItems.item.category', 'bagType']);

        return $travelBag;
    }

    /**
     * Update maximum weight
     */
    public function updateMaxWeight(float $maxWeight, ?string $weightUnit = null)
    {
        $travelBag = $this->getOrCreateMainBag();

        $updateData = ['max_weight' => $maxWeight];
        if ($weightUnit) {
            $updateData['weight_unit'] = $weightUnit;
        }

        $travelBag->update($updateData);
        $travelBag->load('bagItems.item');

        return $travelBag;
    }

    /**
     * Add item to travel bag
     */
    public function addItem(int $itemId, int $quantity = 1, ?float $customWeight = null)
    {
        $travelBag = $this->getOrCreateMainBag();
        $item = Item::find($itemId);

        if (!$item) {
            throw new NotFoundHttpException(LangHelper::msg('item_not_found'));
        }

        // Check if item already exists in bag
        $bagItem = BagItem::where('travel_bag_id', $travelBag->id)
            ->where('item_id', $item->id)
            ->first();

        if ($bagItem) {
            // Update quantity
            $bagItem->quantity += $quantity;
            if ($customWeight !== null) {
                $bagItem->custom_weight = $customWeight;
            }
            $bagItem->save();
        } else {
            // Create new bag item
            $bagItem = BagItem::create([
                'travel_bag_id' => $travelBag->id,
                'item_id' => $item->id,
                'quantity' => $quantity,
                'custom_weight' => $customWeight,
            ]);
        }

        $bagItem->load('item.category');
        $travelBag->load('bagItems.item');

        return [
            'bag_item' => $bagItem,
            'travel_bag' => $travelBag,
        ];
    }

    /**
     * Remove item from travel bag
     */
    public function removeItem(int $itemId, ?int $quantity = null)
    {
        $travelBag = $this->getMainBag();

        $bagItem = BagItem::where('travel_bag_id', $travelBag->id)
            ->where('item_id', $itemId)
            ->first();

        if (!$bagItem) {
            throw new NotFoundHttpException(LangHelper::msg('bag_item_not_found'));
        }

        $quantityToRemove = $quantity ?? $bagItem->quantity;

        if ($quantityToRemove >= $bagItem->quantity) {
            // Remove entire item
            $bagItem->delete();
        } else {
            // Decrease quantity
            $bagItem->quantity -= $quantityToRemove;
            $bagItem->save();
        }

        $travelBag->load('bagItems.item');

        return $travelBag;
    }

    /**
     * Get all items in travel bag
     */
    public function getBagItems()
    {
        $travelBag = TravelBag::where('user_id', Auth::id())
            ->where('bag_type_id', 1) // Main cargo bag ID
            ->first();

        if (!$travelBag) {
            return null;
        }

        $travelBag->load('bagItems.item.category');

        return $travelBag;
    }
}


