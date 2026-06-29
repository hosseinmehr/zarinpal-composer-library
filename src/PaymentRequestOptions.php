<?php

namespace Zarinpal;

/**
 * گزینه‌های پیشرفته درخواست پرداخت (API v4)
 */
class PaymentRequestOptions
{
    /** @var string واحد پولی: ریال */
    public const CURRENCY_IRR = 'IRR';

    /** @var string واحد پولی: تومان */
    public const CURRENCY_IRT = 'IRT';

    /** @var string|null */
    public $email;

    /** @var string|null */
    public $mobile;

    /** @var string|null واحد پولی (IRR یا IRT) */
    public $currency;

    /** @var string|null کد معرف */
    public $referrerId;

    /** @var string|null شماره کارت بانکی */
    public $cardPan;

    /**
     * اعتبارسنجی خودکار تراکنش (auto_verify در metadata)
     * null = طبق تنظیمات پنل زرین‌پال
     *
     * @var bool|null
     */
    public $autoVerify;

    /** @var string|null شناسه سفارش */
    public $orderId;

    /**
     * تسویه اشتراکی شناور (wages)
     * هر عنصر: ['iban' => 'IR...', 'amount' => 10000, 'description' => '...']
     *
     * @var array<int, array<string, mixed>>|null
     */
    public $wages;

    /**
     * میان‌پی (صفحه چک‌اوت) - cart_data
     * شامل items، added_costs و deductions
     *
     * @var array<string, mixed>|null
     */
    public $cartData;

    /**
     * @param string|null $email
     * @param string|null $mobile
     */
    public function __construct($email = null, $mobile = null)
    {
        $this->email = $email;
        $this->mobile = $mobile;
    }

    /**
     * تنظیم واحد پولی
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * فعال/غیرفعال کردن اعتبارسنجی خودکار
     */
    public function setAutoVerify(?bool $autoVerify): self
    {
        $this->autoVerify = $autoVerify;

        return $this;
    }

    /**
     * افزودن تسویه اشتراکی شناور
     *
     * @param array<int, array<string, mixed>> $wages
     */
    public function setWages(array $wages): self
    {
        $this->wages = $wages;

        return $this;
    }

    /**
     * افزودن یک سهم تسهیم
     */
    public function addWage(string $iban, int $amount, string $description): self
    {
        if ($this->wages === null) {
            $this->wages = [];
        }
        $this->wages[] = [
            'iban' => $iban,
            'amount' => $amount,
            'description' => $description,
        ];

        return $this;
    }

    /**
     * تنظیم داده‌های سبد خرید (میان‌پی)
     *
     * @param array<string, mixed> $cartData
     */
    public function setCartData(array $cartData): self
    {
        $this->cartData = $cartData;

        return $this;
    }

    /**
     * ساخت cart_data از آیتم‌های ساده
     *
     * @param array<int, array<string, mixed>> $items
     * @param array<string, int>|null $addedCosts
     * @param array<string, int>|null $deductions
     */
    public function buildCartData(array $items, ?array $addedCosts = null, ?array $deductions = null): self
    {
        $this->cartData = ['items' => $items];
        if ($addedCosts !== null) {
            $this->cartData['added_costs'] = $addedCosts;
        }
        if ($deductions !== null) {
            $this->cartData['deductions'] = $deductions;
        }

        return $this;
    }

    /**
     * تبدیل به آرایه payload برای API v4
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [];

        $metadata = array_filter([
            'mobile' => $this->mobile,
            'email' => $this->email,
            'referrer_id' => $this->referrerId,
            'order_id' => $this->orderId,
            'card_pan' => $this->cardPan,
        ], function ($value) {
            return $value !== null && $value !== '';
        });

        if ($this->autoVerify !== null) {
            $metadata['auto_verify'] = $this->autoVerify;
        }

        if (!empty($metadata)) {
            $payload['metadata'] = $metadata;
        }

        if ($this->currency !== null) {
            $payload['currency'] = $this->currency;
        }

        if ($this->wages !== null) {
            $payload['wages'] = $this->wages;
        }

        if ($this->cartData !== null) {
            $payload['cart_data'] = $this->cartData;
        }

        return $payload;
    }
}
