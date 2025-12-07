<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;
use Illuminate\Http\Client\ConnectionException;

class CurrencyService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.exchangerate.api_key');
        $this->baseUrl = config('services.exchangerate.base_url');
    }

    /**
     * Convert currency from one to another
     *
     * @param string $from Currency code (e.g., USD, EUR, SAR)
     * @param string $to Currency code (e.g., USD, EUR, SAR)
     * @param float $amount Amount to convert
     * @param string|null $date Optional date (YYYY-MM-DD) for historical rates
     * @return array
     * @throws Exception
     */
    public function convert(string $from, string $to, float $amount, ?string $date = null): array
    {
        try {
            $from = strtoupper($from);
            $to = strtoupper($to);

            // حالة التساوي
            if ($from === $to) {
                return [
                    'from' => $from,
                    'to' => $to,
                    'amount' => $amount,
                    'converted_amount' => $amount,
                    'rate' => 1.0,
                    'date' => $date ?? date('Y-m-d'),
                ];
            }

            // Cache key لزوج العملات (من_إلى) والتاريخ
            $cacheKey = "currency_rate_{$from}_{$to}_{$amount}_" . ($date ?? 'latest');
            $cacheTime = $date ? 24 * 60 : 60; // 24 ساعة للتاريخي، 1 ساعة للحديث

            $result = Cache::remember($cacheKey, $cacheTime * 60, function () use ($from, $to, $amount, $date) {
                // ExchangeRate-API endpoint للتحويل المباشر
                if ($date) {
                    // للأسعار التاريخية، نستخدم latest ثم نحسب
                    $url = "{$this->baseUrl}/{$this->apiKey}/history/{$from}/{$date}";
                    $response = Http::timeout(10)->get($url);

                    if ($response->failed()) {
                        // إذا فشل التاريخي، نستخدم latest
                        $url = "{$this->baseUrl}/{$this->apiKey}/pair/{$from}/{$to}/{$amount}";
                        $response = Http::timeout(10)->get($url);
                    } else {
                        $data = $response->json();
                        if ($data['result'] === 'success' && isset($data['conversion_rates'][$to])) {
                            $rate = $data['conversion_rates'][$to];
                            return [
                                'result' => 'success',
                                'conversion_rate' => $rate,
                                'conversion_result' => $amount * $rate,
                                'base_code' => $from,
                                'target_code' => $to,
                                'time_last_update_utc' => $data['time_last_update_utc'] ?? null,
                            ];
                        }
                    }
                } else {
                    // للأسعار الحالية
                    $url = "{$this->baseUrl}/{$this->apiKey}/pair/{$from}/{$to}/{$amount}";
                    $response = Http::timeout(10)->get($url);
                }

                if ($response->failed()) {
                    $error = $response->json();
                    Log::error('ExchangeRate API Error - Convert', [
                        'status' => $response->status(),
                        'error' => $error,
                        'from' => $from,
                        'to' => $to,
                        'amount' => $amount,
                    ]);
                    throw new Exception('Currency conversion failed: ' . ($error['error'] ?? 'Unknown API error'));
                }

                $data = $response->json();

                if ($data['result'] !== 'success') {
                    throw new Exception('Currency conversion failed: ' . ($data['error-type'] ?? 'Unknown error'));
                }

                return $data;
            });

            $rate = $result['conversion_rate'] ?? 0;
            $convertedAmount = $result['conversion_result'] ?? ($amount * $rate);
            $latestDate = $date ?? date('Y-m-d');

            // استخراج التاريخ من time_last_update_utc إذا كان متاحاً
            if (isset($result['time_last_update_utc']) && !$date) {
                try {
                    $dateTime = new \DateTime($result['time_last_update_utc']);
                    $latestDate = $dateTime->format('Y-m-d');
                } catch (\Exception $e) {
                    // في حالة فشل التحليل، نستخدم التاريخ الحالي
                }
            }

            return [
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
                'converted_amount' => round($convertedAmount, 2),
                'rate' => round($rate, 6),
                'date' => $latestDate,
            ];

        } catch (ConnectionException $e) {
            Log::error('ExchangeRate API Connection Error - Convert', [
                'message' => $e->getMessage(),
                'from' => $from,
                'to' => $to,
            ]);
            throw new Exception('Connection failed while reaching currency API: ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('Currency Service General Error - Convert', [
                'message' => $e->getMessage(),
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
            ]);
            throw $e;
        }
    }

    /**
     * Get latest exchange rates for multiple currencies
     *
     * @param string $from Base currency code
     * @param array $to Array of target currency codes
     * @return array
     * @throws Exception
     */
    public function getLatestRates(string $from, array $to): array
    {
        try {
            $from = strtoupper($from);
            $to = array_map('strtoupper', $to);

            $url = "{$this->baseUrl}/{$this->apiKey}/latest/{$from}";
            $cacheKey = "currency_rates_{$from}_" . implode('_', $to);

            $data = Cache::remember($cacheKey, 60 * 60, function () use ($url, $from) {
                $response = Http::timeout(10)->get($url);

                if ($response->failed()) {
                    $error = $response->json();
                    Log::error('ExchangeRate API Error - Latest Rates', [
                        'status' => $response->status(),
                        'error' => $error,
                        'from' => $from,
                    ]);
                    throw new Exception('Failed to fetch exchange rates: ' . ($error['error-type'] ?? 'Unknown API error'));
                }

                $data = $response->json();

                if ($data['result'] !== 'success') {
                    throw new Exception('Failed to fetch exchange rates: ' . ($data['error-type'] ?? 'Unknown error'));
                }

                return $data;
            });

            $rates = [];
            $conversionRates = $data['conversion_rates'] ?? [];

            foreach ($to as $currency) {
                if (isset($conversionRates[$currency])) {
                    $rates[$currency] = $conversionRates[$currency];
                }
            }

            // إذا لم يتم تحديد عملات محددة، نرجع جميع العملات
            if (empty($to)) {
                $rates = $conversionRates;
            }

            $latestDate = date('Y-m-d');
            if (isset($data['time_last_update_utc'])) {
                try {
                    $dateTime = new \DateTime($data['time_last_update_utc']);
                    $latestDate = $dateTime->format('Y-m-d');
                } catch (\Exception $e) {
                    // في حالة فشل التحليل، نستخدم التاريخ الحالي
                }
            }

            return [
                'base' => $from,
                'date' => $latestDate,
                'rates' => $rates,
            ];
        } catch (ConnectionException $e) {
            Log::error('ExchangeRate API Connection Error - Latest Rates', [
                'message' => $e->getMessage(),
                'from' => $from,
            ]);
            throw new Exception('Connection failed while reaching currency API: ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('Currency Service General Error - Latest Rates', [
                'message' => $e->getMessage(),
                'from' => $from,
                'to' => $to,
            ]);
            throw $e;
        }
    }

    /**
     * Get list of available currencies
     */
    public function getCurrencies(): array
    {
        try {
            $url = "{$this->baseUrl}/{$this->apiKey}/codes";
            $cacheKey = 'exchangerate_currencies';

            $data = Cache::remember($cacheKey, 24 * 60 * 60, function () use ($url) {
                $response = Http::timeout(10)->get($url);

                if ($response->failed()) {
                    $error = $response->json();
                    Log::error('ExchangeRate API Error - Currencies', [
                        'status' => $response->status(),
                        'error' => $error,
                    ]);
                    throw new Exception('Failed to fetch currencies: ' . ($error['error-type'] ?? 'Unknown API error'));
                }

                $data = $response->json();

                if ($data['result'] !== 'success') {
                    throw new Exception('Failed to fetch currencies: ' . ($data['error-type'] ?? 'Unknown error'));
                }

                return $data;
            });

            // تحويل من [["USD", "United States Dollar"], ...] إلى ["USD" => "United States Dollar", ...]
            $currencies = [];
            if (isset($data['supported_codes']) && is_array($data['supported_codes'])) {
                foreach ($data['supported_codes'] as $code) {
                    if (is_array($code) && count($code) >= 2) {
                        $currencies[$code[0]] = $code[1];
                    }
                }
            }

            return $currencies;
        } catch (ConnectionException $e) {
            Log::error('ExchangeRate API Connection Error - Currencies', [
                'message' => $e->getMessage(),
            ]);
            throw new Exception('Connection failed while reaching currency API: ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('Currency Service General Error - Currencies', [
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get historical exchange rate for a specific date (بدون تغيير)
     */
    public function getHistoricalRate(string $date, string $from, string $to): array
    {
        // تعيد استخدام convert والتي تم تعديلها الآن
        return $this->convert($from, $to, 1.0, $date);
    }

    /**
     * Get the latest date available from API
     */
    protected function getLatestDate(): string
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/{$this->apiKey}/latest/USD");
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['time_last_update_utc'])) {
                    try {
                        $dateTime = new \DateTime($data['time_last_update_utc']);
                        return $dateTime->format('Y-m-d');
                    } catch (\Exception $e) {
                        // في حالة فشل التحليل، نستخدم التاريخ الحالي
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get latest date from API', ['error' => $e->getMessage()]);
        }

        return date('Y-m-d');
    }
}
