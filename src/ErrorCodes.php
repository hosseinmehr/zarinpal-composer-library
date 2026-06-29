<?php

namespace Zarinpal;

/**
 * کدهای خطا و وضعیت درگاه پرداخت زرین‌پال (API v4)
 * منبع: https://www.zarinpal.com/docs/paymentGateway/errorList
 */
class ErrorCodes
{
    /** @var int موفقیت */
    public const SUCCESS = 100;

    /** @var int تراکنش قبلاً وریفای شده */
    public const ALREADY_VERIFIED = 101;

    /**
     * نگاشت کد خطا به پیام انگلیسی و فارسی
     *
     * @var array<int, array{en: string, fa: string, type: string}>
     */
    public const MESSAGES = [
        -1 => [
            'en' => 'Merchant ID is not set.',
            'fa' => 'مرچنت کد در تنظیمات وارد نشده است.',
            'type' => 'public',
        ],
        -2 => [
            'en' => 'Callback URL is not set.',
            'fa' => 'آدرس بازگشت (callback_url) وارد نشده است.',
            'type' => 'public',
        ],
        -3 => [
            'en' => 'Description is missing or exceeds 500 characters.',
            'fa' => 'توضیحات وارد نشده یا بیش از ۵۰۰ کاراکتر است.',
            'type' => 'public',
        ],
        -4 => [
            'en' => 'Payment amount is out of allowed range.',
            'fa' => 'مبلغ پرداختی کمتر یا بیشتر از حد مجاز است.',
            'type' => 'public',
        ],
        -5 => [
            'en' => 'Referrer ID is invalid.',
            'fa' => 'کد معرف (referrer_id) نامعتبر است.',
            'type' => 'public',
        ],
        -9 => [
            'en' => 'Validation error.',
            'fa' => 'خطای اعتبارسنجی. پارامترهای ورودی را بررسی کنید.',
            'type' => 'public',
        ],
        -10 => [
            'en' => 'Terminal is not valid.',
            'fa' => 'ای پی یا مرچنت کد پذیرنده صحیح نیست.',
            'type' => 'public',
        ],
        -11 => [
            'en' => 'Terminal is not active.',
            'fa' => 'درگاه فعال نیست. با پشتیبانی زرین‌پال تماس بگیرید.',
            'type' => 'public',
        ],
        -12 => [
            'en' => 'Too many attempts.',
            'fa' => 'تلاش بیش از حد مجاز. لطفاً بعداً تلاش کنید.',
            'type' => 'public',
        ],
        -15 => [
            'en' => 'Terminal user is suspended.',
            'fa' => 'کاربر پذیرنده غیرفعال شده است.',
            'type' => 'public',
        ],
        -16 => [
            'en' => 'Terminal user level is not valid.',
            'fa' => 'سطح تایید پذیرنده پایین‌تر از سطح نقره‌ای است.',
            'type' => 'public',
        ],
        -17 => [
            'en' => 'Terminal user level is not valid.',
            'fa' => 'سطح تایید پذیرنده برای این تراکنش کافی نیست.',
            'type' => 'public',
        ],
        -18 => [
            'en' => 'Referrer ID is invalid.',
            'fa' => 'کد معرف نامعتبر است.',
            'type' => 'public',
        ],
        -19 => [
            'en' => 'Terminal user transactions are suspended.',
            'fa' => 'تراکنش‌های پذیرنده مسدود شده است.',
            'type' => 'public',
        ],
        -21 => [
            'en' => 'Invalid host.',
            'fa' => 'آی‌پی سرور معتبر نیست. آی‌پی را در پنل زرین‌پال ثبت کنید.',
            'type' => 'public',
        ],
        -22 => [
            'en' => 'Merchant IP is not valid.',
            'fa' => 'آی‌پی پذیرنده معتبر نیست.',
            'type' => 'public',
        ],
        -30 => [
            'en' => 'Terminal does not allow floating wages.',
            'fa' => 'پذیرنده اجازه دسترسی به سرویس تسویه اشتراکی شناور را ندارد.',
            'type' => 'PaymentRequest',
        ],
        -31 => [
            'en' => 'Terminal does not allow wages. Add default bank account in panel.',
            'fa' => 'حساب بانکی تسویه را به پنل اضافه کنید. مقادیر تسهیم درست نیست.',
            'type' => 'PaymentRequest',
        ],
        -32 => [
            'en' => 'Total floating wages exceed max amount.',
            'fa' => 'مجموع مبالغ تسهیم شناور از مبلغ کل تراکنش بیشتر است.',
            'type' => 'PaymentRequest',
        ],
        -33 => [
            'en' => 'Floating wages percentages are not valid.',
            'fa' => 'درصدهای تسهیم شناور صحیح نیست.',
            'type' => 'PaymentRequest',
        ],
        -34 => [
            'en' => 'Total fixed wages exceed max amount.',
            'fa' => 'مجموع مبالغ تسهیم ثابت از مبلغ کل تراکنش بیشتر است.',
            'type' => 'PaymentRequest',
        ],
        -35 => [
            'en' => 'Too many wage recipients.',
            'fa' => 'تعداد افراد دریافت‌کننده تسهیم بیش از حد مجاز است.',
            'type' => 'PaymentRequest',
        ],
        -36 => [
            'en' => 'Minimum wage amount is 10000 Rials.',
            'fa' => 'حداقل مبلغ جهت تسهیم باید ۱۰٬۰۰۰ ریال باشد.',
            'type' => 'PaymentRequest',
        ],
        -37 => [
            'en' => 'One or more IBANs are inactive.',
            'fa' => 'یک یا چند شماره شبای تسهیم از سمت بانک غیرفعال است.',
            'type' => 'PaymentRequest',
        ],
        -38 => [
            'en' => 'IBAN definition error. Try again later.',
            'fa' => 'خطا در تعریف شبا. لطفاً دقایقی دیگر تلاش کنید.',
            'type' => 'PaymentRequest',
        ],
        -39 => [
            'en' => 'Wages processing error. Contact Zarinpal support.',
            'fa' => 'خطا در پردازش تسهیم. با پشتیبانی زرین‌پال تماس بگیرید.',
            'type' => 'PaymentRequest',
        ],
        -40 => [
            'en' => 'Invalid extra params.',
            'fa' => 'پارامترهای اضافی نامعتبر است.',
            'type' => 'PaymentRequest',
        ],
        -41 => [
            'en' => 'Maximum payment amount is 100 million Toman.',
            'fa' => 'حداکثر مبلغ پرداختی ۱۰۰ میلیون تومان است.',
            'type' => 'PaymentRequest',
        ],
        -50 => [
            'en' => 'Paid amount does not match verify amount.',
            'fa' => 'مبلغ پرداخت شده با مبلغ ارسالی در وریفای متفاوت است.',
            'type' => 'PaymentVerify',
        ],
        -51 => [
            'en' => 'Payment was not successful.',
            'fa' => 'پرداخت ناموفق بوده یا توسط کاربر لغو شده است.',
            'type' => 'PaymentVerify',
        ],
        -52 => [
            'en' => 'Unexpected error. Contact support.',
            'fa' => 'خطای غیرمنتظره. با پشتیبانی زرین‌پال تماس بگیرید.',
            'type' => 'PaymentVerify',
        ],
        -53 => [
            'en' => 'Authority does not belong to this merchant.',
            'fa' => 'تراکنش متعلق به این مرچنت کد نیست.',
            'type' => 'PaymentVerify',
        ],
        -54 => [
            'en' => 'Invalid authority.',
            'fa' => 'اتوریتی نامعتبر است.',
            'type' => 'PaymentVerify',
        ],
        -55 => [
            'en' => 'Manual payment request not found.',
            'fa' => 'تراکنش مورد نظر یافت نشد.',
            'type' => 'PaymentVerify',
        ],
        -60 => [
            'en' => 'Session cannot be reversed with bank.',
            'fa' => 'امکان ریورس کردن تراکنش با بانک وجود ندارد.',
            'type' => 'PaymentReverse',
        ],
        -61 => [
            'en' => 'Session is not in success state.',
            'fa' => 'تراکنش در وضعیت موفق نیست.',
            'type' => 'PaymentReverse',
        ],
        -62 => [
            'en' => 'Reverse time limit exceeded.',
            'fa' => 'مهلت ریورس تراکنش به پایان رسیده است.',
            'type' => 'PaymentReverse',
        ],
        -98 => [
            'en' => 'HTTP connection error.',
            'fa' => 'خطا در اتصال به سرور زرین‌پال.',
            'type' => 'public',
        ],
        -99 => [
            'en' => 'Unknown response from gateway.',
            'fa' => 'پاسخ نامعتبر از درگاه پرداخت.',
            'type' => 'public',
        ],
        100 => [
            'en' => 'Success.',
            'fa' => 'عملیات با موفقیت انجام شد.',
            'type' => 'success',
        ],
        101 => [
            'en' => 'Already verified.',
            'fa' => 'تراکنش قبلاً وریفای شده است.',
            'type' => 'success',
        ],
    ];

    /**
     * پیام انگلیسی کد خطا
     */
    public static function messageEn(int $code): string
    {
        return self::MESSAGES[$code]['en'] ?? 'Unknown error.';
    }

    /**
     * پیام فارسی کد خطا
     */
    public static function messageFa(int $code): string
    {
        return self::MESSAGES[$code]['fa'] ?? 'خطای ناشناخته.';
    }

    /**
     * نوع خطا (public, PaymentRequest, PaymentVerify, ...)
     */
    public static function type(int $code): string
    {
        return self::MESSAGES[$code]['type'] ?? 'unknown';
    }

    /**
     * آیا کد نشان‌دهنده موفقیت است؟
     */
    public static function isSuccess(int $code): bool
    {
        return in_array($code, [self::SUCCESS, self::ALREADY_VERIFIED], true);
    }

    /**
     * استخراج کد خطا از پاسخ API
     *
     * @param array<string, mixed> $response
     */
    public static function extractCode(array $response): int
    {
        if (!empty($response['data']['code'])) {
            return (int) $response['data']['code'];
        }

        if (!empty($response['errors']) && is_array($response['errors'])) {
            foreach ($response['errors'] as $fieldErrors) {
                if (!is_array($fieldErrors)) {
                    continue;
                }
                foreach ($fieldErrors as $item) {
                    if (is_numeric($item)) {
                        return (int) $item;
                    }
                }
            }
            if (isset($response['errors']['code']) && is_numeric($response['errors']['code'])) {
                return (int) $response['errors']['code'];
            }
        }

        return -99;
    }
}
