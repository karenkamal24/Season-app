<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TravelBagResource;
use App\Http\Resources\BagItemResource;
use App\Http\Requests\UpdateMaxWeightRequest;
use App\Http\Requests\AddItemRequest;
use App\Http\Requests\RemoveItemRequest;
use App\Http\Requests\UpdateItemQuantityRequest;
use App\Services\TravelBagService;
use App\Services\GeminiAIService;
use App\Utils\ApiResponse;
use App\Helpers\LangHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TravelBagController extends Controller
{
    public function __construct(
        protected TravelBagService $travelBagService,
        protected GeminiAIService $geminiService
    ) {}
    /**
     * Get Travel Bag Details
     * GET /api/travel-bag/details?bag_type_id={id}
     * If bag_type_id is not provided, returns all user's bags
     */
    public function details(Request $request)
    {
        try {
            $bagTypeId = $request->query('bag_type_id');

            // If bag_type_id is provided and not empty, return single bag
            if ($bagTypeId !== null && $bagTypeId !== '') {
                $travelBag = $this->travelBagService->getOrCreateBagByType((int)$bagTypeId);

                // Get bag type name based on locale
                $locale = app()->getLocale();
                $bagTypeName = $locale === 'ar'
                    ? ($travelBag->bagType->name_ar ?? '')
                    : ($travelBag->bagType->name_en ?? '');

                // Create success message with bag name
                $successMessage = str_replace(':bag_name', $bagTypeName, LangHelper::msg('travel_bag_fetched_with_name'));

                return ApiResponse::send(
                    Response::HTTP_OK,
                    $successMessage,
                    new TravelBagResource($travelBag)
                );
            }

            // If bag_type_id is not provided, return all user's bags
            $travelBags = $this->travelBagService->getAllUserBags();

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('travel_bags_fetched'),
                TravelBagResource::collection($travelBags)
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('travel_bag_fetch_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Update Maximum Weight
     * PUT /api/travel-bag/max-weight
     */
    public function updateMaxWeight(UpdateMaxWeightRequest $request)
    {
        try {
            $validated = $request->validated();
            $travelBag = $this->travelBagService->updateMaxWeight(
                $validated['max_weight'],
                $validated['weight_unit'] ?? null,
                $validated['bag_type_id'] ?? 1  // Default to main cargo bag (ID = 1)
            );

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('travel_bag_weight_updated'),
                [
                    'max_weight' => round((float) $travelBag->max_weight, 2),
                    'current_weight' => round((float) $travelBag->current_weight, 2),
                    'weight_percentage' => round((float) $travelBag->weight_percentage, 2),
                ]
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('travel_bag_weight_update_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Add Item to Travel Bag
     * POST /api/travel-bag/add-item
     */
    public function addItem(AddItemRequest $request)
    {
        try {
            $validated = $request->validated();
            $result = $this->travelBagService->addItem(
                $validated['item_id'] ?? null,
                $validated['quantity'] ?? 1,
                $validated['custom_weight'] ?? null,
                $validated['bag_type_id'] ?? 1,  // Default to main cargo bag (ID = 1)
                $validated['custom_item_name'] ?? null
            );

            $bagItem = $result['bag_item'];
            $travelBag = $result['travel_bag'];

            // Get bag type name based on locale
            $locale = app()->getLocale();
            $bagTypeName = $locale === 'ar'
                ? ($travelBag->bagType->name_ar ?? '')
                : ($travelBag->bagType->name_en ?? '');

            // Create success message with bag name
            $successMessage = str_replace(':bag_name', $bagTypeName, LangHelper::msg('item_added_to_bag_with_name'));

            // Calculate weight
            $weight = $bagItem->custom_weight;
            if ($bagItem->item_id && $bagItem->item) {
                $weight = $bagItem->custom_weight ?? $bagItem->item->default_weight;
            }
            $totalWeight = $weight * $bagItem->quantity;

            return ApiResponse::send(
                Response::HTTP_OK,
                $successMessage,
                [
                    'item_added' => new BagItemResource($bagItem),
                    'bag_type_id' => $travelBag->bag_type_id,
                    'bag_name' => $bagTypeName,
                    'updated_bag' => [
                        'current_weight' => round((float) $travelBag->current_weight, 2),
                        'max_weight' => round((float) $travelBag->max_weight, 2),
                        'weight_percentage' => round((float) $travelBag->weight_percentage, 2),
                        'total_items' => $travelBag->bagItems->sum('quantity'),
                    ]
                ]
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('item_add_to_bag_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove Item from Travel Bag
     * DELETE /api/travel-bag/items/{item_id}?bag_type_id={id}&quantity={qty}
     */
    public function removeItem(RemoveItemRequest $request, $itemId)
    {
        try {
            $validated = $request->validated();
            $travelBag = $this->travelBagService->removeItem(
                $itemId,
                $validated['quantity'] ?? null,
                $validated['bag_type_id']
            );

            // Get bag type name based on locale
            $locale = app()->getLocale();
            $bagTypeName = $locale === 'ar'
                ? ($travelBag->bagType->name_ar ?? '')
                : ($travelBag->bagType->name_en ?? '');

            // Create success message with bag name
            $successMessage = str_replace(':bag_name', $bagTypeName, LangHelper::msg('item_removed_from_bag_with_name'));

            return ApiResponse::send(
                Response::HTTP_OK,
                $successMessage,
                [
                    'bag_type_id' => $travelBag->bag_type_id,
                    'bag_name' => $bagTypeName,
                    'updated_bag' => [
                        'current_weight' => round((float) $travelBag->current_weight, 2),
                        'max_weight' => round((float) $travelBag->max_weight, 2),
                        'weight_percentage' => round((float) $travelBag->weight_percentage, 2),
                        'total_items' => $travelBag->bagItems->sum('quantity'),
                    ]
                ]
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('item_remove_from_bag_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Update Item Quantity in Travel Bag
     * PUT /api/travel-bag/items/{item_id}/quantity
     */
    public function updateItemQuantity(UpdateItemQuantityRequest $request, $itemId)
    {
        try {
            $validated = $request->validated();
            $result = $this->travelBagService->updateItemQuantity(
                $itemId,
                $validated['quantity'],
                $validated['bag_type_id']
            );

            $bagItem = $result['bag_item'];
            $travelBag = $result['travel_bag'];

            // Get bag type name based on locale
            $locale = app()->getLocale();
            $bagTypeName = $locale === 'ar'
                ? ($travelBag->bagType->name_ar ?? '')
                : ($travelBag->bagType->name_en ?? '');

            // Create success message with bag name
            $successMessage = str_replace(':bag_name', $bagTypeName, LangHelper::msg('item_quantity_updated_with_name'));

            return ApiResponse::send(
                Response::HTTP_OK,
                $successMessage,
                [
                    'item_updated' => new BagItemResource($bagItem),
                    'bag_type_id' => $travelBag->bag_type_id,
                    'bag_name' => $bagTypeName,
                    'updated_bag' => [
                        'current_weight' => round((float) $travelBag->current_weight, 2),
                        'max_weight' => round((float) $travelBag->max_weight, 2),
                        'weight_percentage' => round((float) $travelBag->weight_percentage, 2),
                        'total_items' => $travelBag->bagItems->sum('quantity'),
                    ]
                ]
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('item_quantity_update_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Get Travel Bag Items
     * GET /api/travel-bag/items?bag_type_id={id}
     */
    public function getItems(Request $request)
    {
        try {
            $bagTypeId = $request->query('bag_type_id', 1); // Default to main cargo bag (ID = 1)
            $travelBag = $this->travelBagService->getBagItems($bagTypeId);

            if (!$travelBag) {
                return ApiResponse::send(
                    Response::HTTP_OK,
                    LangHelper::msg('travel_bag_items_fetched'),
                    [
                        'items' => [],
                        'total_weight' => 0,
                        'total_items' => 0,
                        'max_weight' => 0,
                        'weight_percentage' => 0,
                    ]
                );
            }

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('travel_bag_items_fetched'),
                [
                    'bag_type_id' => $travelBag->bag_type_id,
                    'bag_name' => app()->getLocale() === 'ar'
                        ? ($travelBag->bagType->name_ar ?? '')
                        : ($travelBag->bagType->name_en ?? ''),
                    'items' => BagItemResource::collection($travelBag->bagItems),
                    'total_weight' => round((float) $travelBag->current_weight, 2),
                    'max_weight' => round((float) $travelBag->max_weight, 2),
                    'weight_percentage' => round((float) $travelBag->weight_percentage, 2),
                    'total_items' => $travelBag->bagItems->sum('quantity'),
                ]
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('travel_bag_items_fetch_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Get travel bag reminder
     * GET /api/travel-bag/reminder?bag_type_id={id}
     */
    public function getReminder(Request $request)
    {
        try {
            $bagTypeId = $request->query('bag_type_id', 1); // Default to main cargo bag (ID = 1)
            $travelBag = $this->travelBagService->getOrCreateBagByType((int)$bagTypeId);

            // Get all reminders related to this travel bag (trip reminder + daily reminder)
            $reminders = \App\Models\Reminder::where('travel_bag_id', $travelBag->id)
                ->where('status', 'active')
                ->orderBy('recurrence') // 'once' reminders first, then 'daily'
                ->get();

            if ($reminders->isEmpty()) {
                return ApiResponse::send(
                    Response::HTTP_OK,
                    LangHelper::msg('travel_bag_reminder_fetched'),
                    [
                        'bag_type_id' => $travelBag->bag_type_id,
                        'bag_name' => app()->getLocale() === 'ar'
                            ? ($travelBag->bagType->name_ar ?? '')
                            : ($travelBag->bagType->name_en ?? ''),
                        'reminders' => [],
                        'has_reminder' => false,
                    ]
                );
            }

            // Format reminders
            $formattedReminders = $reminders->map(function ($reminder) {
                $date = $reminder->date ? $reminder->date->format('Y-m-d') : null;

                $timeValue = $reminder->time;
                if (is_string($timeValue)) {
                    if (preg_match('/^(\d{1,2}):(\d{2})/', $timeValue, $matches)) {
                        $time = $matches[1] . ':' . $matches[2];
                    } else {
                        $time = $timeValue;
                    }
                } elseif (is_object($timeValue) && method_exists($timeValue, 'format')) {
                    $time = $timeValue->format('H:i');
                } else {
                    $time = null;
                }

                return [
                    'reminder_id' => $reminder->id,
                    'title' => $reminder->title,
                    'date' => $date,
                    'time' => $time,
                    'timezone' => $reminder->timezone ?? 'Africa/Cairo',
                    'recurrence' => $reminder->recurrence,
                    'notes' => $reminder->notes,
                    'status' => $reminder->status,
                    'last_sent_at' => $reminder->last_sent_at ? $reminder->last_sent_at->toIso8601String() : null,
                    'created_at' => $reminder->created_at ? $reminder->created_at->toIso8601String() : null,
                ];
            });

            // Get bag type name based on locale
            $locale = app()->getLocale();
            $bagTypeName = $locale === 'ar'
                ? ($travelBag->bagType->name_ar ?? '')
                : ($travelBag->bagType->name_en ?? '');

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('travel_bag_reminder_fetched'),
                [
                    'bag_type_id' => $travelBag->bag_type_id,
                    'bag_name' => $bagTypeName,
                    'reminders' => $formattedReminders,
                    'has_reminder' => true,
                    'trip_reminder' => $formattedReminders->firstWhere('recurrence', 'once'),
                    'daily_reminder' => $formattedReminders->firstWhere('recurrence', 'daily'),
                ]
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('travel_bag_reminder_fetch_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Set travel date - يحدد موعد سفر ويوقف التذكير اليومي
     * POST /api/travel-bag/travel-date
     * body: { "bag_type_id": 2, "date": "2025-02-15", "time": "08:30", "timezone": "Africa/Cairo" }
     *
     * هذا الـ endpoint يختلف عن setReminder:
     * - يلغي التذكير اليومي تلقائياً
     * - ينشئ تذكير واحد (once) لميعاد السفر
     * - التذكير يبعت فقط لو الشنطة ready (ممتلئة)
     */
    public function setTravelDate(Request $request)
    {
        try {
            $validated = $request->validate([
                'bag_type_id' => ['required', 'integer', 'min:1'],
                'date' => ['required', 'date_format:Y-m-d'],
                'time' => ['required', 'date_format:H:i'],
                'timezone' => ['nullable', 'string'],
            ]);

            $result = $this->travelBagService->setTravelDate(
                (int) $validated['bag_type_id'],
                $validated['date'],
                $validated['time'],
                $validated['timezone'] ?? null,
            );

            // Get bag type name based on locale
            $locale = app()->getLocale();
            $bagTypeName = $locale === 'ar'
                ? ($result['travel_bag']->bagType->name_ar ?? '')
                : ($result['travel_bag']->bagType->name_en ?? '');

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('travel_date_set'),
                [
                    'bag_type_id' => $result['travel_bag']->bag_type_id,
                    'bag_name' => $bagTypeName,
                    'travel_date' => $validated['date'],
                    'travel_time' => $validated['time'],
                    'reminder' => [
                        'reminder_id' => $result['reminder']->id,
                        'title' => $result['reminder']->title,
                        'date' => $result['reminder']->date->format('Y-m-d'),
                        'time' => $result['reminder']->time,
                        'recurrence' => $result['reminder']->recurrence,
                        'status' => $result['reminder']->status,
                    ],
                    'bag_status' => $result['travel_bag']->is_ready ? 'ready' : 'not_ready',
                    'message' => $result['travel_bag']->is_ready
                        ? (LangHelper::msg('travel_date_set_bag_ready') ?? 'تم تحديد موعد السفر. الشنطة جاهزة وسيتم إرسال التذكير في الموعد المحدد.')
                        : (LangHelper::msg('travel_date_set_bag_not_ready') ?? 'تم تحديد موعد السفر. سيتم إرسال التذكير فقط عندما تكون الشنطة جاهزة (ممتلئة).'),
                ]
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::send(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                LangHelper::msg('validation_failed'),
                $e->errors()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('travel_date_set_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Set travel reminder (date/time) for a travel bag
     * POST /api/travel-bag/reminder
     * body: { "bag_type_id": 2, "date": "2025-02-15", "time": "08:30", "timezone": "Africa/Cairo" }
     */
    public function setReminder(Request $request)
    {
        try {
            $validated = $request->validate([
                'bag_type_id' => ['required', 'integer', 'min:1'],
                'date' => ['required', 'date_format:Y-m-d'],
                'time' => ['required', 'date_format:H:i'],
                'timezone' => ['nullable', 'string'],
            ]);

            $result = $this->travelBagService->setBagReminder(
                (int) $validated['bag_type_id'],
                $validated['date'],
                $validated['time'],
                $validated['timezone'] ?? null,
            );

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('reminder_created'),
                new TravelBagResource($result['travel_bag'])
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::send(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                LangHelper::msg('validation_failed'),
                $e->errors()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('reminder_create_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Get AI-generated packing categories
     * GET /api/travel-bag/ai/categories
     * Uses Accept-Language header (ar/en)
     */
    public function getAICategories(Request $request)
    {
        try {
            // Get language from query parameter (if provided), otherwise from Accept-Language header
            // Use same method as ItemCategoryController
            $language = $request->query('language');

            if (!$language) {
                // Get from Accept-Language header (same as ItemCategoryController)
                $headerLang = $request->header('Accept-Language', 'ar');
                $language = strtolower(explode('-', trim($headerLang))[0]);
            }

            // Validate language, default to 'ar' if not supported
            if (!in_array($language, ['ar', 'en'])) {
                $language = 'ar';
            }

            // Get AI-generated categories
            $categories = $this->geminiService->generatePackingCategories($language);

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('ai_categories_generated') ?? 'AI categories generated successfully',
                [
                    'categories' => $categories,
                    'language' => $language,
                ]
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                (LangHelper::msg('categories_retrieval_failed') ?? 'Failed to retrieve categories') . ': ' . $e->getMessage()
            );
        }
    }

    /**
     * Get AI-suggested items for a category
     * GET /api/travel-bag/ai/suggest-items?category={category_name}
     * Uses Accept-Language header (ar/en)
     */
    public function getAISuggestedItems(Request $request)
    {
        try {
            $categoryName = $request->query('category');

            if (!$categoryName) {
                return ApiResponse::send(
                    Response::HTTP_BAD_REQUEST,
                    LangHelper::msg('category_required') ?? 'Category parameter is required',
                    []
                );
            }

            // Get language from query parameter (if provided), otherwise from Accept-Language header
            $language = $request->query('language');

            if (!$language) {
                // Get from Accept-Language header
                $headerLang = $request->header('Accept-Language', 'ar');
                $language = strtolower(explode('-', trim($headerLang))[0]);
            }

            // Validate language, default to 'ar' if not supported
            if (!in_array($language, ['ar', 'en'])) {
                $language = 'ar';
            }

            // Get AI suggestions for this category
            $items = $this->geminiService->suggestItemsForCategory($categoryName, $language);

            // Convert weight from grams to kilograms for consistency with the app
            $items = array_map(function ($item) {
                if (isset($item['weight']) && is_numeric($item['weight'])) {
                    // Convert grams to kilograms (divide by 1000)
                    $item['weight'] = round($item['weight'] / 1000, 3);
                    $item['weight_grams'] = (int)$item['weight'] * 1000; // Keep original in grams for reference
                }
                return $item;
            }, $items);

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('ai_items_suggested') ?? 'AI items suggested successfully',
                [
                    'category' => $categoryName,
                    'items' => $items,
                    'language' => $language,
                ]
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                (LangHelper::msg('ai_items_suggestion_failed') ?? 'Failed to suggest AI items') . ': ' . $e->getMessage()
            );
        }
    }

    /**
     * Add AI-suggested item to travel bag
     * POST /api/travel-bag/ai/add-item
     * body: {
     *   "item_name": "string",
     *   "weight": float (in kg),
     *   "essential": boolean,
     *   "bag_type_id": int (optional, default: 1),
     *   "quantity": int (optional, default: 1)
     * }
     */
    public function addAIItem(Request $request)
    {
        try {
            $validated = $request->validate([
                'item_name' => ['required', 'string', 'max:255'],
                'weight' => ['required', 'numeric', 'min:0', 'max:999.99'],
                'essential' => ['nullable', 'boolean'],
                'bag_type_id' => ['nullable', 'integer', 'min:1'],
                'quantity' => ['nullable', 'integer', 'min:1'],
            ]);

            $bagTypeId = $validated['bag_type_id'] ?? 1;
            $quantity = $validated['quantity'] ?? 1;
            $essential = $validated['essential'] ?? false;
            $itemName = $validated['item_name'];
            $weight = $validated['weight'];

            // Add item using TravelBagService
            // Note: We'll need to update TravelBagService to support essential flag
            $result = $this->travelBagService->addAIItem(
                $itemName,
                $weight,
                $essential,
                $bagTypeId,
                $quantity
            );

            $bagItem = $result['bag_item'];
            $travelBag = $result['travel_bag'];

            // Get bag type name based on locale
            $locale = app()->getLocale();
            $bagTypeName = $locale === 'ar'
                ? ($travelBag->bagType->name_ar ?? '')
                : ($travelBag->bagType->name_en ?? '');

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('ai_item_added') ?? 'AI item added successfully',
                [
                    'item_added' => new BagItemResource($bagItem),
                    'bag_type_id' => $travelBag->bag_type_id,
                    'bag_name' => $bagTypeName,
                    'updated_bag' => [
                        'current_weight' => round((float) $travelBag->current_weight, 2),
                        'max_weight' => round((float) $travelBag->max_weight, 2),
                        'weight_percentage' => round((float) $travelBag->weight_percentage, 2),
                        'total_items' => $travelBag->bagItems->sum('quantity'),
                    ]
                ]
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::send(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                LangHelper::msg('validation_failed') ?? 'Validation failed',
                $e->errors()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                (LangHelper::msg('ai_item_add_failed') ?? 'Failed to add AI item') . ': ' . $e->getMessage()
            );
        }
    }

    /**
     * Estimate weight for a custom item using AI
     * POST /api/travel-bag/estimate-weight
     */
    public function estimateWeight(Request $request)
    {
        try {
            $request->validate([
                'custom_item_name' => 'required|string|max:255',
            ], [
                'custom_item_name.required' => 'Item name is required',
                'custom_item_name.string' => 'Item name must be a string',
                'custom_item_name.max' => 'Item name must not exceed 255 characters',
            ]);

            $itemName = $request->input('custom_item_name');
            $language = app()->getLocale();

            // Estimate weight using AI
            $estimatedWeight = $this->geminiService->estimateItemWeight($itemName, $language);

            return ApiResponse::send(
                Response::HTTP_OK,
                'Weight estimated successfully',
                [
                    'item_name' => $itemName,
                    'estimated_weight_kg' => $estimatedWeight,
                    'estimated_weight_grams' => round($estimatedWeight * 1000, 1),
                ]
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::badRequest($e->getMessage());
        } catch (\Exception $e) {
            Log::error('Failed to estimate item weight', [
                'item_name' => $request->input('custom_item_name'),
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::send(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to estimate weight: ' . $e->getMessage(),
                []
            );
        }
    }
}
