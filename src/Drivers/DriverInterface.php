<?php

namespace Zarinpal\Drivers;

interface DriverInterface
{
    /**
     * ایجاد درخواست پرداخت
     *
     * @param array<string, mixed> $inputs
     *
     * @return array<string, mixed>
     */
    public function request(array $inputs);

    /**
     * وریفای تراکنش
     *
     * @param array<string, mixed> $inputs
     *
     * @return array<string, mixed>
     */
    public function verify(array $inputs);

    /**
     * استعلام وضعیت تراکنش (بدون وریفای)
     *
     * @param array<string, mixed> $inputs
     *
     * @return array<string, mixed>
     */
    public function inquiry(array $inputs);

    /**
     * لیست تراکنش‌های وریفای‌نشده
     *
     * @param array<string, mixed> $inputs
     *
     * @return array<string, mixed>
     */
    public function unverified(array $inputs);

    /**
     * ریورس تراکنش
     *
     * @param array<string, mixed> $inputs
     *
     * @return array<string, mixed>
     */
    public function reverse(array $inputs);

    /**
     * محاسبه کارمزد
     *
     * @param array<string, mixed> $inputs
     *
     * @return array<string, mixed>
     */
    public function feeCalculation(array $inputs);

    /**
     * تنظیم آدرس پایه API
     */
    public function setAddress(string $baseUrl);

    /**
     * فعال‌سازی محیط سندباکس
     */
    public function enableSandbox();
}
