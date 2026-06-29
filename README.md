# zarinpal-composer-library

[![Tests](https://github.com/hosseinmehr/zarinpal-composer-library/actions/workflows/php.yml/badge.svg)](https://github.com/hosseinmehr/zarinpal-composer-library/actions/workflows/php.yml)

**نسخه:** 1.0.1

کتابخانه PHP برای اتصال به [درگاه پرداخت زرین‌پال](https://www.zarinpal.com/docs/paymentGateway/) بر پایه **API v4**.

این پکیج امکان ایجاد درخواست پرداخت، وریفای، استعلام، تسویه اشتراکی شناور، میان‌پی (چک‌اوت)، واحد پولی و اعتبارسنجی خودکار/غیرخودکار را فراهم می‌کند.

## نصب

```bash
composer require hosseinmehr/zarinpal-composer-library
```

## پیش‌نیاز

- PHP 7.2 یا بالاتر
- Guzzle HTTP Client
- کد درگاه پرداخت (`merchant_id`) از [پنل زرین‌پال](https://www.zarinpal.com)

## شروع سریع

```php
use Zarinpal\Zarinpal;

$zarinpal = new Zarinpal('XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX');
$zarinpal->enableSandbox(); // محیط تست

$results = $zarinpal->request(
    'https://example.com/payment/callback',
    10000,
    'خرید محصول شماره ۱۲۳'
);

if (!empty($results['Authority'])) {
    // ذخیره Authority برای مرحله وریفای
    $zarinpal->redirect();
}
```

### وریفای پرداخت

پس از بازگشت کاربر، پارامتر `Status` در QueryString مقدار `OK` یا `NOK` دارد:

```php
$status = $_GET['Status'] ?? 'NOK';
$authority = $_GET['Authority'] ?? '';

$results = $zarinpal->verify($status, 10000, $authority);

if ($results['Status'] === 'success') {
    echo 'شماره تراکنش: ' . $results['RefID'];
}
```

## ویژگی‌های پیشرفته

### واحد پولی (ریال / تومان)

```php
use Zarinpal\PaymentRequestOptions;

$options = new PaymentRequestOptions('user@example.com', '09121234567');
$options->setCurrency(PaymentRequestOptions::CURRENCY_IRT); // تومان

$results = $zarinpal->request(
    'https://example.com/callback',
    1000,
    'خرید اشتراک',
    null,
    null,
    $options
);
```

مقادیر مجاز: `IRR` (ریال، پیش‌فرض) و `IRT` (تومان).

### اعتبارسنجی خودکار / غیرخودکار

با پارامتر `auto_verify` در `metadata` می‌توانید رفتار اعتبارسنجی را کنترل کنید:

```php
$options = new PaymentRequestOptions();
$options->setAutoVerify(true);  // اعتبارسنجی خودکار
// $options->setAutoVerify(false); // اعتبارسنجی غیرخودکار

$results = $zarinpal->request($callbackUrl, 10000, 'توضیحات', null, null, $options);
```

> اگر `auto_verify` ارسال نشود، رفتار طبق تنظیمات پنل زرین‌پال تعیین می‌شود.
> [مستندات اعتبارسنجی](https://www.zarinpal.com/docs/paymentGateway/moreFeatures/session-validation)

### تسویه اشتراکی شناور (تسهیم)

```php
$options = new PaymentRequestOptions();
$options->addWage('IR123456789012345678901234', 5000, 'سهم فروشنده اول');
$options->addWage('IR987654321098765432109876', 3000, 'سهم فروشنده دوم');

$results = $zarinpal->request($callbackUrl, 10000, 'خرید با تسهیم', null, null, $options);
```

### میان‌پی (صفحه چک‌اوت)

ارسال جزئیات سبد خرید برای نمایش شفاف‌تر به خریدار:

```php
$options = new PaymentRequestOptions('user@example.com', '09120000000');
$options->buildCartData(
    [
        [
            'item_name' => 'کفش ورزشی',
            'item_amount' => 50000,
            'item_count' => 2,
            'item_amount_sum' => 100000,
        ],
    ],
    ['tax' => 5000, 'transport' => 2000],
    ['discount' => 3000]
);

$results = $zarinpal->request($callbackUrl, 150000, 'سفارش ۱۰۱۰', null, null, $options);
```

[مستندات میان‌پی](https://www.zarinpal.com/docs/paymentGateway/moreFeatures/checkout)

## متدهای دیگر

### استعلام وضعیت (بدون وریفای)

```php
$result = $zarinpal->inquiry($authority);
// status: VERIFIED | PAID | IN_BANK | FAILED | REVERSED
```

### تراکنش‌های وریفای‌نشده

```php
$result = $zarinpal->unverified();
```

### ریورس تراکنش

```php
$result = $zarinpal->reverse($authority);
```

### محاسبه کارمزد

```php
$result = $zarinpal->feeCalculation(10000, PaymentRequestOptions::CURRENCY_IRR);
```

## کدهای خطا

```php
use Zarinpal\ErrorCodes;

if (!$results['success']) {
    echo ErrorCodes::messageFa($results['code']);
    echo ErrorCodes::messageEn($results['code']);
}
```

کلاس `ErrorCodes` شامل تمام کدهای خطای مستندات زرین‌پال است:
[لیست خطاها](https://www.zarinpal.com/docs/paymentGateway/errorList)

## یکپارچه‌سازی با Laravel

این کتابخانه با **Laravel 5.5 به بالا** (شامل Laravel 8، 9، 10 و 11) سازگار است.

امکانات Laravel:
- **Service Provider** — `Zarinpal\Laravel\ZarinpalServiceProvider`
- **Facade** — `Zarinpal::request()`، `Zarinpal::verify()` و سایر متدها
- **Auto-discovery** — در Laravel 5.5+ پس از نصب، Provider و Facade خودکار ثبت می‌شوند

### نصب در پروژه Laravel

```bash
composer require hosseinmehr/zarinpal-composer-library
```

### تنظیمات

در فایل `.env` پروژه:

```env
ZARINPAL_MERCHANT_ID=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
ZARINPAL_SANDBOX=true
ZARINPAL_ZARINGATE=false
```

در فایل `config/services.php`:

```php
'zarinpal' => [
    'merchantID' => env('ZARINPAL_MERCHANT_ID'),
    'sandbox' => env('ZARINPAL_SANDBOX', false),
    'zarinGate' => env('ZARINPAL_ZARINGATE', false),
],
```

یا انتشار فایل پیکربندی اختصاصی:

```bash
php artisan vendor:publish --tag=zarinpal-config
```

### نمونه Controller

```php
use Illuminate\Http\Request;
use Zarinpal\Laravel\Facade\Zarinpal;
use Zarinpal\PaymentRequestOptions;

public function pay()
{
    $options = (new PaymentRequestOptions(
        auth()->user()->email,
        auth()->user()->phone
    ))
        ->setCurrency(PaymentRequestOptions::CURRENCY_IRR)
        ->setAutoVerify(false);

    $result = Zarinpal::request(
        route('payment.callback'),
        10000,
        'خرید سفارش #' . $order->id,
        null,
        null,
        $options
    );

    if (!empty($result['Authority'])) {
        return redirect(Zarinpal::redirectUrl());
    }

    return back()->withErrors($result['message_fa'] ?? 'خطا در اتصال به درگاه');
}

public function callback(Request $request)
{
    $result = Zarinpal::verify(
        $request->get('Status'),
        10000,
        $request->get('Authority')
    );

    if ($result['Status'] === 'success') {
        // پرداخت موفق — شماره تراکنش: $result['RefID']
    }

    return redirect()->route('home')->withErrors($result['message_fa'] ?? 'پرداخت ناموفق');
}
```

### استفاده با Facade

```php
use Zarinpal\Laravel\Facade\Zarinpal;
use Zarinpal\PaymentRequestOptions;

$options = (new PaymentRequestOptions())
    ->setCurrency(PaymentRequestOptions::CURRENCY_IRR)
    ->setAutoVerify(true);

$results = Zarinpal::request($callbackUrl, 10000, 'خرید', null, null, $options);

if (!empty($results['Authority'])) {
    return redirect(Zarinpal::redirectUrl());
}
```

### استفاده با Dependency Injection

به‌جای Facade می‌توانید از container لاراول استفاده کنید:

```php
public function pay(\Zarinpal\Zarinpal $zarinpal)
{
    $result = $zarinpal->request(route('payment.callback'), 10000, 'خرید');
    // ...
}

// یا:
$zarinpal = app('Zarinpal');
```

### نکات Laravel

1. **Laravel 11+** — همان روش کار می‌کند؛ فقط `services.php` را خودتان تنظیم کنید.
2. **محیط تست** — در `.env` مقدار `ZARINPAL_SANDBOX=true` بگذارید.
3. **Route نمونه:**

```php
Route::get('/payment', [PaymentController::class, 'pay'])->name('payment.pay');
Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
```

## سازگاری با نسخه قبل

- امضای `verify('OK', $amount, $authority)` همچنان پشتیبانی می‌شود.
- فرمت قدیمی `AdditionalData` با کلید `Wages` به فرمت v4 تبدیل می‌شود.
- کلیدهای `Authority` و `RefID` در پاسخ‌ها حفظ شده‌اند.

## تست

```bash
composer test
```

تست‌های یکپارچه‌سازی به مرچنت سندباکس واقعی نیاز دارند:

```bash
ZARINPAL_SANDBOX_MERCHANT_ID=your-sandbox-merchant composer test
```

## مستندات رسمی

- [راهنمای درگاه پرداخت](https://www.zarinpal.com/docs/paymentGateway/)
- [لیست خطاها](https://www.zarinpal.com/docs/paymentGateway/errorList)
- [اعتبارسنجی تراکنش](https://www.zarinpal.com/docs/paymentGateway/moreFeatures/session-validation)
- [میان‌پی (چک‌اوت)](https://www.zarinpal.com/docs/paymentGateway/moreFeatures/checkout)

## مجوز

GPL-2.0-only — مشاهده [LICENSE.md](LICENSE.md)

## مشارکت

مخزن: [github.com/hosseinmehr/zarinpal-composer-library](https://github.com/hosseinmehr/zarinpal-composer-library)
