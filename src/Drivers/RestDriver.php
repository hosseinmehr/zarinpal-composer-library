<?php

namespace Zarinpal\Drivers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Zarinpal\ErrorCodes;

/**
 * درایور REST برای API v4 درگاه پرداخت زرین‌پال
 */
class RestDriver implements DriverInterface
{
    /** @var string */
    protected $apiBaseUrl = 'https://payment.zarinpal.com/pg/v4/payment/';

    /** @var string */
    protected $redirectBaseUrl = 'https://www.zarinpal.com/pg/StartPay/';

    /**
     * @param array<string, mixed> $inputs
     *
     * @return array<string, mixed>
     */
    public function request(array $inputs)
    {
        $response = $this->apiCall('request.json', $inputs);

        return $this->normalizeRequestResponse($response);
    }

    /**
     * @param array<string, mixed> $inputs
     *
     * @return array<string, mixed>
     */
    public function verify(array $inputs)
    {
        $response = $this->apiCall('verify.json', $inputs);

        return $this->normalizeVerifyResponse($response);
    }

    /**
     * @param array<string, mixed> $inputs
     *
     * @return array<string, mixed>
     */
    public function inquiry(array $inputs)
    {
        $response = $this->apiCall('inquiry.json', $inputs);

        return $this->normalizeDataResponse($response);
    }

    /**
     * @param array<string, mixed> $inputs
     *
     * @return array<string, mixed>
     */
    public function unverified(array $inputs)
    {
        $response = $this->apiCall('unVerified.json', $inputs);

        return $this->normalizeDataResponse($response);
    }

    /**
     * @param array<string, mixed> $inputs
     *
     * @return array<string, mixed>
     */
    public function reverse(array $inputs)
    {
        $response = $this->apiCall('reverse.json', $inputs);

        return $this->normalizeDataResponse($response);
    }

    /**
     * @param array<string, mixed> $inputs
     *
     * @return array<string, mixed>
     */
    public function feeCalculation(array $inputs)
    {
        $response = $this->apiCall('feeCalculation.json', $inputs);

        return $this->normalizeDataResponse($response);
    }

    /**
     * آدرس هدایت کاربر به درگاه پرداخت
     */
    public function getRedirectUrl(string $authority, bool $zarinGate = false): string
    {
        $url = $this->redirectBaseUrl . $authority;
        if ($zarinGate) {
            $url .= '/ZarinGate';
        }

        return $url;
    }

    public function setAddress(string $baseUrl)
    {
        $this->apiBaseUrl = rtrim($baseUrl, '/') . '/';
    }

    public function enableSandbox()
    {
        $this->apiBaseUrl = 'https://sandbox.zarinpal.com/pg/v4/payment/';
        $this->redirectBaseUrl = 'https://sandbox.zarinpal.com/pg/StartPay/';
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function apiCall(string $uri, array $data)
    {
        try {
            $client = new Client([
                'base_uri' => $this->apiBaseUrl,
                'timeout' => 30,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);
            $response = $client->request('POST', $uri, ['json' => $data]);
            $body = json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            if ($response !== null) {
                $body = json_decode($response->getBody()->getContents(), true);
            } else {
                $body = [
                    'data' => [],
                    'errors' => ['message' => 'HTTP connection error', 'code' => -98],
                ];
            }
        }

        if (!is_array($body)) {
            $body = ['data' => [], 'errors' => ['message' => 'Invalid JSON response', 'code' => -99]];
        }

        return $body;
    }

    /**
     * @param array<string, mixed> $response
     *
     * @return array<string, mixed>
     */
    private function normalizeRequestResponse(array $response)
    {
        $code = ErrorCodes::extractCode($response);

        if (ErrorCodes::isSuccess($code) && !empty($response['data']['authority'])) {
            $data = $response['data'];

            return [
                'success' => true,
                'code' => $code,
                'message' => $data['message'] ?? ErrorCodes::messageEn($code),
                'message_fa' => ErrorCodes::messageFa($code),
                'Authority' => $data['authority'],
                'authority' => $data['authority'],
                'fee_type' => $data['fee_type'] ?? null,
                'fee' => $data['fee'] ?? null,
                'data' => $data,
            ];
        }

        return $this->errorResponse($code, $response);
    }

    /**
     * @param array<string, mixed> $response
     *
     * @return array<string, mixed>
     */
    private function normalizeVerifyResponse(array $response)
    {
        $code = ErrorCodes::extractCode($response);

        if ($code === ErrorCodes::SUCCESS) {
            $data = $response['data'];

            return [
                'success' => true,
                'Status' => 'success',
                'code' => $code,
                'message' => $data['message'] ?? ErrorCodes::messageEn($code),
                'message_fa' => ErrorCodes::messageFa($code),
                'RefID' => $data['ref_id'] ?? null,
                'ref_id' => $data['ref_id'] ?? null,
                'card_pan' => $data['card_pan'] ?? null,
                'card_hash' => $data['card_hash'] ?? null,
                'fee_type' => $data['fee_type'] ?? null,
                'fee' => $data['fee'] ?? null,
                'wages' => $data['wages'] ?? null,
                'data' => $data,
            ];
        }

        if ($code === ErrorCodes::ALREADY_VERIFIED) {
            $data = $response['data'];

            return [
                'success' => true,
                'Status' => 'verified_before',
                'code' => $code,
                'message' => $data['message'] ?? ErrorCodes::messageEn($code),
                'message_fa' => ErrorCodes::messageFa($code),
                'RefID' => $data['ref_id'] ?? null,
                'ref_id' => $data['ref_id'] ?? null,
                'card_pan' => $data['card_pan'] ?? null,
                'card_hash' => $data['card_hash'] ?? null,
                'fee_type' => $data['fee_type'] ?? null,
                'fee' => $data['fee'] ?? null,
                'wages' => $data['wages'] ?? null,
                'data' => $data,
            ];
        }

        return $this->errorResponse($code, $response);
    }

    /**
     * @param array<string, mixed> $response
     *
     * @return array<string, mixed>
     */
    private function normalizeDataResponse(array $response)
    {
        $code = ErrorCodes::extractCode($response);

        if (ErrorCodes::isSuccess($code) && !empty($response['data'])) {
            $data = $response['data'];

            return [
                'success' => true,
                'Status' => 'success',
                'code' => $code,
                'message' => $data['message'] ?? ErrorCodes::messageEn($code),
                'message_fa' => ErrorCodes::messageFa($code),
                'data' => $data,
            ] + $data;
        }

        return $this->errorResponse($code, $response);
    }

    /**
     * @param array<string, mixed> $response
     *
     * @return array<string, mixed>
     */
    private function errorResponse(int $code, array $response)
    {
        $apiMessage = $response['message'] ?? ($response['errors']['message'] ?? null);

        return [
            'success' => false,
            'Status' => 'error',
            'code' => $code,
            'error' => $code,
            'message' => $apiMessage ?: ErrorCodes::messageEn($code),
            'message_fa' => ErrorCodes::messageFa($code),
            'error_type' => ErrorCodes::type($code),
            'errorInfo' => $response['errors'] ?? $response,
            'errors' => $response['errors'] ?? [],
        ];
    }
}
