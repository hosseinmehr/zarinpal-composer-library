<?php

namespace Zarinpal;

use Zarinpal\Drivers\DriverInterface;
use Zarinpal\Drivers\RestDriver;

class Zarinpal
{
    /** @var string */
    private $merchantID;

    /** @var DriverInterface */
    private $driver;

    /** @var string|null */
    private $Authority;

    /** @var bool */
    private $zarinGate = false;

    /**
     * @param string $merchantID
     * @param DriverInterface|null $driver
     */
    public function __construct($merchantID, DriverInterface $driver = null)
    {
        if (is_null($driver)) {
            $driver = new RestDriver();
        }
        $this->merchantID = $merchantID;
        $this->driver = $driver;
    }

    /**
     * ایجاد درخواست پرداخت
     *
     * @param string $callbackURL
     * @param int $amount
     * @param string $description
     * @param string|null $email
     * @param string|null $mobile
     * @param PaymentRequestOptions|array|null $options گزینه‌های پیشرفته یا داده اضافی (سازگاری با نسخه قبل)
     *
     * @return array<string, mixed>
     */
    public function request($callbackURL, $amount, $description, $email = null, $mobile = null, $options = null)
    {
        $payload = [
            'merchant_id' => $this->merchantID,
            'callback_url' => $callbackURL,
            'amount' => (int) $amount,
            'description' => $description,
        ];

        $requestOptions = $this->resolveRequestOptions($options, $email, $mobile);
        if ($requestOptions !== null) {
            $payload = array_merge($payload, $requestOptions->toArray());
        } elseif ($email !== null || $mobile !== null) {
            $payload['metadata'] = array_filter([
                'email' => $email,
                'mobile' => $mobile,
            ]);
        }

        $results = $this->driver->request($payload);

        $this->Authority = $results['Authority'] ?? ($results['authority'] ?? null);
        if (empty($this->Authority)) {
            $results['Authority'] = null;
        }

        return $results;
    }

    /**
     * وریفای تراکنش
     *
     * @param int|string $amountOrStatus مبلغ یا Status بازگشتی (سازگاری با نسخه قبل)
     * @param int|string|null $authorityOrAmount
     * @param string|null $authority
     *
     * @return array<string, mixed>
     */
    public function verify($amountOrStatus, $authorityOrAmount = null, $authority = null)
    {
        // سازگاری با امضای قدیمی: verify('OK', amount, authority)
        if (func_num_args() === 3) {
            $status = $amountOrStatus;
            $amount = $authorityOrAmount;
            $authority = $authority;

            if (strtoupper((string) $status) !== 'OK') {
                return [
                    'success' => false,
                    'Status' => 'canceled',
                    'code' => -51,
                    'error' => -51,
                    'message' => 'Payment was canceled by user.',
                    'message_fa' => 'پرداخت توسط کاربر لغو شده یا ناموفق بوده است.',
                ];
            }
        } else {
            $amount = $amountOrStatus;
            $authority = $authorityOrAmount;
        }

        $inputs = [
            'merchant_id' => $this->merchantID,
            'authority' => $authority,
            'amount' => (int) $amount,
        ];

        return $this->driver->verify($inputs);
    }

    /**
     * استعلام وضعیت تراکنش (بدون وریفای)
     *
     * @return array<string, mixed>
     */
    public function inquiry($authority)
    {
        return $this->driver->inquiry([
            'merchant_id' => $this->merchantID,
            'authority' => $authority,
        ]);
    }

    /**
     * دریافت لیست تراکنش‌های وریفای‌نشده
     *
     * @return array<string, mixed>
     */
    public function unverified()
    {
        return $this->driver->unverified([
            'merchant_id' => $this->merchantID,
        ]);
    }

    /**
     * ریورس تراکنش
     *
     * @return array<string, mixed>
     */
    public function reverse($authority)
    {
        return $this->driver->reverse([
            'merchant_id' => $this->merchantID,
            'authority' => $authority,
        ]);
    }

    /**
     * محاسبه کارمزد تراکنش
     *
     * @param string $currency IRR یا IRT
     *
     * @return array<string, mixed>
     */
    public function feeCalculation($amount, $currency = PaymentRequestOptions::CURRENCY_IRR)
    {
        return $this->driver->feeCalculation([
            'merchant_id' => $this->merchantID,
            'amount' => (int) $amount,
            'currency' => $currency,
        ]);
    }

    /**
     * هدایت کاربر به درگاه پرداخت
     */
    public function redirect()
    {
        header('Location: ' . $this->redirectUrl());
        exit;
    }

    /**
     * آدرس هدایت به درگاه پرداخت
     *
     * @return string
     */
    public function redirectUrl()
    {
        if ($this->driver instanceof RestDriver) {
            return $this->driver->getRedirectUrl((string) $this->Authority, $this->zarinGate);
        }

        $url = 'https://www.zarinpal.com/pg/StartPay/' . $this->Authority;
        if ($this->zarinGate) {
            $url .= '/ZarinGate';
        }

        return $url;
    }

    /**
     * @return DriverInterface
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * فعال‌سازی محیط سندباکس
     */
    public function enableSandbox()
    {
        $this->driver->enableSandbox();
    }

    /**
     * فعال‌سازی حالت زرین‌گیت
     */
    public function isZarinGate()
    {
        $this->zarinGate = true;
    }

    /**
     * تبدیل فرمت قدیمی AdditionalData به PaymentRequestOptions
     *
     * @param PaymentRequestOptions|array|null $options
     * @param string|null $email
     * @param string|null $mobile
     *
     * @return PaymentRequestOptions|null
     */
    private function resolveRequestOptions($options, $email, $mobile)
    {
        if ($options instanceof PaymentRequestOptions) {
            if ($email !== null) {
                $options->email = $email;
            }
            if ($mobile !== null) {
                $options->mobile = $mobile;
            }

            return $options;
        }

        if (!is_array($options)) {
            return null;
        }

        $requestOptions = new PaymentRequestOptions($email, $mobile);

        // فرمت قدیمی: ['Wages' => ['zp.1.1' => ['Amount' => ..., 'Description' => ...]]]
        if (isset($options['Wages']) && is_array($options['Wages'])) {
            $wages = [];
            foreach ($options['Wages'] as $iban => $wage) {
                $wages[] = [
                    'iban' => $iban,
                    'amount' => (int) ($wage['Amount'] ?? $wage['amount'] ?? 0),
                    'description' => (string) ($wage['Description'] ?? $wage['description'] ?? ''),
                ];
            }
            $requestOptions->setWages($wages);
        }

        if (isset($options['currency'])) {
            $requestOptions->setCurrency((string) $options['currency']);
        }
        if (isset($options['auto_verify'])) {
            $requestOptions->setAutoVerify((bool) $options['auto_verify']);
        }
        if (isset($options['cart_data'])) {
            $requestOptions->setCartData($options['cart_data']);
        }
        if (isset($options['wages']) && is_array($options['wages'])) {
            $requestOptions->setWages($options['wages']);
        }
        if (isset($options['referrer_id'])) {
            $requestOptions->referrerId = $options['referrer_id'];
        }
        if (isset($options['order_id'])) {
            $requestOptions->orderId = $options['order_id'];
        }
        if (isset($options['card_pan'])) {
            $requestOptions->cardPan = $options['card_pan'];
        }

        return $requestOptions;
    }
}
