<?php

namespace Zarinpal\Laravel\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array request(string $callbackURL, int $amount, string $description, string $email = null, string $mobile = null, $options = null)
 * @method static array verify($amountOrStatus, $authorityOrAmount = null, $authority = null)
 * @method static array inquiry(string $authority)
 * @method static array unverified()
 * @method static array reverse(string $authority)
 * @method static array feeCalculation(int $amount, string $currency = 'IRR')
 * @method static void redirect()
 * @method static string redirectUrl()
 * @method static \Zarinpal\Drivers\DriverInterface getDriver()
 * @method static void enableSandbox()
 * @method static void isZarinGate()
 */
class Zarinpal extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Zarinpal';
    }
}
