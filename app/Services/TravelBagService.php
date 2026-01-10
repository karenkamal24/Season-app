<?php

namespace App\Services;

use App\Models\TravelBag;
use App\Models\BagItem;
use App\Models\BagType;
use App\Models\Item;
use App\Models\Reminder;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Helpers\LangHelper;
use Carbon\Carbon;

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

        // Update bag status based on weight
        $this->updateBagStatusBasedOnWeight($travelBag);

        return [
            'bag_item' => $bagItem,
            'travel_bag' => $travelBag,
        ];
    }

    /**
     * Add AI-suggested item to travel bag
     */
    public function addAIItem(string $itemName, float $weight, bool $essential = false, int $bagTypeId = 1, int $quantity = 1)
    {
        $travelBag = $this->getOrCreateBagByType($bagTypeId);

        // Check if current weight already exceeds max weight
        $currentWeight = $travelBag->current_weight;
        if ($currentWeight >= $travelBag->max_weight) {
            throw new \Exception(LangHelper::msg('cannot_add_more_weight_exceeded'));
        }

        // Check if custom item with same name already exists
        $bagItem = BagItem::where('travel_bag_id', $travelBag->id)
            ->where('custom_item_name', $itemName)
            ->whereNull('item_id')
            ->first();

        // Calculate current weight (excluding this item if it exists)
        $currentWeight = $travelBag->current_weight;

        if ($bagItem) {
            // Remove existing item weight from current weight
            $existingItemWeight = $bagItem->custom_weight ?? $weight;
            $currentWeight -= ($existingItemWeight * $bagItem->quantity);

            // Calculate new total weight after adding quantity
            $newQuantity = $bagItem->quantity + $quantity;
            $newTotalWeight = $currentWeight + ($weight * $newQuantity);
        } else {
            // Calculate new total weight for new item
            $newTotalWeight = $currentWeight + ($weight * $quantity);
        }

        // Check if adding this item will exceed max weight
        if ($newTotalWeight > $travelBag->max_weight) {
            throw new \Exception(LangHelper::msg('cannot_add_more_weight_exceeded'));
        }

        if ($bagItem) {
            // Update quantity and essential flag
            $bagItem->quantity += $quantity;
            $bagItem->custom_weight = $weight;
            $bagItem->custom_item_name = $itemName;
            // Update essential flag if column exists (check fillable or use schema check)
            $fillable = $bagItem->getFillable();
            if (in_array('essential', $fillable) || Schema::hasColumn('bag_items', 'essential')) {
                $bagItem->essential = $essential;
            }
            $bagItem->save();
        } else {
            // Create new bag item
            $bagItemData = [
                'travel_bag_id' => $travelBag->id,
                'item_id' => null,
                'custom_item_name' => $itemName,
                'quantity' => $quantity,
                'custom_weight' => $weight,
            ];
            
            // Add essential flag if column exists
            $fillable = (new BagItem())->getFillable();
            if (in_array('essential', $fillable) || Schema::hasColumn('bag_items', 'essential')) {
                $bagItemData['essential'] = $essential;
            }
            
            $bagItem = BagItem::create($bagItemData);
        }

        // Load relationships
        $travelBag->load(['bagItems.item.category', 'bagType']);

        // Update bag status based on weight
        $this->updateBagStatusBasedOnWeight($travelBag);

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

        // Update bag status based on weight
        $this->updateBagStatusBasedOnWeight($travelBag);

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

        // Update bag status based on weight
        $this->updateBagStatusBasedOnWeight($travelBag);

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
            'bagType',
            'tripReminder',
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
                'bagType',
                'tripReminder',
            ])
            ->get();

        return $travelBags;
    }

    /**
     * Set travel date - يحدد موعد سفر ويوقف التذكير اليومي
     * التذكير يبعت فقط لو الشنطة ready (ممتلئة)
     */
    public function setTravelDate(int $bagTypeId, string $date, string $time, ?string $timezone = null): array
    {
        $user = Auth::user();
        $travelBag = $this->getOrCreateBagByType($bagTypeId);
        $userLang = $user->preferred_language ?? app()->getLocale() ?? 'ar';

        // Cancel previous active once-reminders for this bag
        Reminder::where('user_id', $user->id)
            ->where('travel_bag_id', $travelBag->id)
            ->where('recurrence', 'once')
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        // بمجرد ما يحدد ميعاد سفر للشنطة، نوقف الريمايندر اليومي
        // اللي بيقول "شنطة السفر لسه مش كاملة"
        $this->cancelNotFullReminder($travelBag);

        // العنوان والنص حسب لغة المستخدم
        if ($userLang === 'en') {
            $title = 'Trip reminder for your travel bag';
            $notes = "Your trip is scheduled on {$date} at {$time}. This reminder will be sent only when your bag is ready (full).";
        } else {
            $title = 'تذكير برحلتك لهذه الحقيبة';
            $notes = "رحلتك مجدولة بتاريخ {$date} الساعة {$time}. سيتم إرسال هذا التذكير فقط عندما تكون الشنطة جاهزة (ممتلئة).";
        }

        $reminder = Reminder::create([
            'user_id' => $user->id,
            'travel_bag_id' => $travelBag->id,
            'title' => $title,
            'date' => $date,
            'time' => $time,
            'timezone' => $timezone ?: 'Africa/Cairo',
            'recurrence' => 'once',
            'notes' => $notes,
            'status' => 'active',
        ]);

        // أول ما يحدد موعد سفر، الشنطة تبقى ready تلقائياً
        $travelBag->status = 'ready';
        $travelBag->save();

        // Reload bag with reminder relation
        $travelBag->load([
            'bagItems.item.category',
            'bagType',
            'tripReminder',
        ]);

        return [
            'travel_bag' => $travelBag,
            'reminder' => $reminder,
        ];
    }

    /**
     * Set travel reminder (date/time) for a specific bag type
     * and link it to the travel bag.
     */
    public function setBagReminder(int $bagTypeId, string $date, string $time, ?string $timezone = null): array
    {
        $user = Auth::user();
        $travelBag = $this->getOrCreateBagByType($bagTypeId);
        $userLang = $user->preferred_language ?? app()->getLocale() ?? 'ar';

        // Cancel previous active once-reminders for this bag
        Reminder::where('user_id', $user->id)
            ->where('travel_bag_id', $travelBag->id)
            ->where('recurrence', 'once')
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        // بمجرد ما يحدد ميعاد سفر للشنطة، نوقف الريمايندر اليومي
        // اللي بيقول "شنطة السفر لسه مش كاملة"
        $this->cancelNotFullReminder($travelBag);

        // العنوان والنص حسب لغة المستخدم (آخر Accept-Language محفوظة)
        if ($userLang === 'en') {
            $title = 'Trip reminder for your travel bag';
            $notes = "Trip reminder for your travel bag on {$date} at {$time}.";
        } else {
            $title = 'تذكير برحلتك لهذه الحقيبة';
            $notes = "تذكير برحلتك لهذه الحقيبة بتاريخ {$date} الساعة {$time}.";
        }

        $reminder = Reminder::create([
            'user_id' => $user->id,
            'travel_bag_id' => $travelBag->id,
            'title' => $title,
            'date' => $date,
            'time' => $time,
            // نفس نظام التذكيرات العادي: نحترم الـ timezone اللي جاي من الـ API
            // ولو مش مبعوت نستخدم Africa/Cairo كافتراضي (زي ما بنعمل في باقي السيستم).
            'timezone' => $timezone ?: 'Africa/Cairo',
            'recurrence' => 'once',
            'notes' => $notes,
            'status' => 'active',
        ]);

        // Reload bag with reminder relation
        $travelBag->load([
            'bagItems.item.category',
            'bagType',
            'tripReminder',
        ]);

        return [
            'travel_bag' => $travelBag,
            'reminder' => $reminder,
        ];
    }

    /**
     * Update bag status based on current weight and max weight
     * and manage the auto-reminder for "bag not full yet".
     */
    protected function updateBagStatusBasedOnWeight(TravelBag $travelBag): void
    {
        // لو الشنطة ready بسبب موعد سفر (في reminder مربوط بيها)، ما نغيرش الحالة
        $hasTravelDateReminder = Reminder::where('travel_bag_id', $travelBag->id)
            ->where('recurrence', 'once')
            ->where('status', 'active')
            ->exists();

        if ($hasTravelDateReminder && $travelBag->status === 'ready') {
            // الشنطة جاهزة بسبب موعد السفر، ما نغيرش الحالة
            // بس نتأكد إن التذكير اليومي ملغى
            $this->cancelNotFullReminder($travelBag);
            return;
        }

        // Determine status from weight
        $status = $travelBag->is_ready ? 'ready' : 'not_ready';

        if ($travelBag->status !== $status) {
            $travelBag->status = $status;
            $travelBag->save();
        }

        // Auto-manage daily reminder:
        // - If bag has items and is not_ready -> ensure daily reminder exists
        // - If bag is ready OR empty          -> cancel existing "not full yet" reminder
        $bagHasItems = $travelBag->bagItems && $travelBag->bagItems->count() > 0;

        if ($bagHasItems && $status === 'not_ready') {
            $this->ensureNotFullReminderExists($travelBag);
        } else {
            $this->cancelNotFullReminder($travelBag);
        }
    }

    /**
     * Ensure there is a daily reminder for this user's travel bag
     * while it is not full yet.
     */
    protected function ensureNotFullReminderExists(TravelBag $travelBag): void
    {
        $user = $travelBag->user;
        if (!$user) {
            return;
        }

        $title = 'Your travel bag is not full yet | شنطة السفر لسه مش كاملة';

        $existing = Reminder::where('user_id', $user->id)
            ->where('recurrence', 'daily')
            ->where('status', 'active')
            ->where('title', $title)
            ->first();

        if ($existing) {
            return;
        }

        $nowCairo = Carbon::now('Africa/Cairo');

        Reminder::create([
            'user_id' => $user->id,
            'title' => $title,
            'date' => $nowCairo->toDateString(),
            // Fixed time once per day; scheduler + recurrence=daily
            // will take care of sending it every day around this time.
            'time' => '21:00',
            'timezone' => 'Africa/Cairo',
            'recurrence' => 'daily',
            'notes' => "Your travel bag is not full yet. You still have space to add more items.\n" .
                "شنطة السفر بتاعتك لسه مش كاملة، لسه في مساحة تضيف حاجات تانية.",
            'status' => 'active',
        ]);
    }

    /**
     * Cancel the "bag not full yet" daily reminder when it is no longer needed.
     */
    protected function cancelNotFullReminder(TravelBag $travelBag): void
    {
        $user = $travelBag->user;
        if (!$user) {
            return;
        }

        $title = 'Your travel bag is not full yet | شنطة السفر لسه مش كاملة';

        Reminder::where('user_id', $user->id)
            ->where('recurrence', 'daily')
            ->where('status', 'active')
            ->where('title', $title)
            ->update(['status' => 'completed']);
    }
}

