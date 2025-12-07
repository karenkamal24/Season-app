<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use App\Utils\ApiResponse;
use App\Helpers\LangHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    protected CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Convert currency from one to another
     * POST /api/currency/convert
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function convert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
            'amount' => 'required|numeric|min:0',
            'date' => 'nullable|date|date_format:Y-m-d|before_or_equal:today',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError(
                LangHelper::msg('validation_error') ?? 'Validation failed',
                $validator->errors()->toArray()
            );
        }

        try {
            $from = strtoupper($request->input('from'));
            $to = strtoupper($request->input('to'));
            $amount = (float) $request->input('amount');
            $date = $request->input('date');

            $result = $this->currencyService->convert($from, $to, $amount, $date);

            return ApiResponse::success(
                LangHelper::msg('currency_converted') ?? 'Currency converted successfully',
                $result
            );
        } catch (Exception $e) {
            Log::error('Currency Controller Convert Error', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return ApiResponse::error(
                LangHelper::msg('currency_conversion_error') ?? 'Failed to convert currency: ' . $e->getMessage()
            );
        }
    }

    /**
     * Get latest exchange rates
     * GET /api/currency/latest
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function latest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required|string|size:3',
            'to' => 'nullable|array',
            'to.*' => 'string|size:3',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError(
                LangHelper::msg('validation_error') ?? 'Validation failed',
                $validator->errors()->toArray()
            );
        }

        try {
            $from = strtoupper($request->input('from'));
            $to = $request->input('to', []);

            // If no 'to' currencies specified, get all available currencies
            if (empty($to)) {
                $currencies = $this->currencyService->getCurrencies();
                $to = array_keys($currencies);
                // Remove the base currency from the list
                $to = array_filter($to, function ($currency) use ($from) {
                    return $currency !== $from;
                });
            }

            $result = $this->currencyService->getLatestRates($from, $to);

            return ApiResponse::success(
                LangHelper::msg('exchange_rates_retrieved') ?? 'Exchange rates retrieved successfully',
                $result
            );
        } catch (Exception $e) {
            Log::error('Currency Controller Latest Error', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return ApiResponse::error(
                LangHelper::msg('exchange_rates_error') ?? 'Failed to fetch exchange rates: ' . $e->getMessage()
            );
        }
    }

    /**
     * Get list of available currencies
     * GET /api/currency/currencies
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function currencies()
    {
        try {
            $currencies = $this->currencyService->getCurrencies();

            return ApiResponse::success(
                LangHelper::msg('currencies_retrieved') ?? 'Currencies retrieved successfully',
                [
                    'currencies' => $currencies,
                    'count' => count($currencies),
                ]
            );
        } catch (Exception $e) {
            Log::error('Currency Controller Currencies Error', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return ApiResponse::error(
                LangHelper::msg('currencies_error') ?? 'Failed to fetch currencies: ' . $e->getMessage()
            );
        }
    }

    /**
     * Get historical exchange rate for a specific date
     * GET /api/currency/historical
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function historical(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|date_format:Y-m-d|before_or_equal:today',
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError(
                LangHelper::msg('validation_error') ?? 'Validation failed',
                $validator->errors()->toArray()
            );
        }

        try {
            $date = $request->input('date');
            $from = strtoupper($request->input('from'));
            $to = strtoupper($request->input('to'));

            $result = $this->currencyService->getHistoricalRate($date, $from, $to);

            return ApiResponse::success(
                LangHelper::msg('historical_rate_retrieved') ?? 'Historical rate retrieved successfully',
                $result
            );
        } catch (Exception $e) {
            Log::error('Currency Controller Historical Error', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return ApiResponse::error(
                LangHelper::msg('historical_rate_error') ?? 'Failed to fetch historical rate: ' . $e->getMessage()
            );
        }
    }
}

