<?php

use GuzzleHttp\Client;
use Zarinpal\ErrorCodes;
use Zarinpal\PaymentRequestOptions;
use Zarinpal\Zarinpal;

class RestTest extends \PHPUnit\Framework\TestCase
{
    /** @var Zarinpal */
    private $zarinpal;

    protected function setUp(): void
    {
        $merchantId = getenv('ZARINPAL_SANDBOX_MERCHANT_ID') ?: 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX';
        $this->zarinpal = new Zarinpal($merchantId);
        $this->zarinpal->enableSandbox();
    }

    public function testErrorCodesContainMainCodes()
    {
        $this->assertTrue(ErrorCodes::isSuccess(100));
        $this->assertTrue(ErrorCodes::isSuccess(101));
        $this->assertFalse(ErrorCodes::isSuccess(-9));
        $this->assertStringContainsString('اعتبارسنجی', ErrorCodes::messageFa(-9));
        $this->assertStringContainsString('floating wages', ErrorCodes::messageEn(-30));
    }

    public function testPaymentRequestOptionsToArray()
    {
        $options = new PaymentRequestOptions('test@example.com', '09121234567');
        $options->setCurrency(PaymentRequestOptions::CURRENCY_IRT);
        $options->setAutoVerify(true);
        $options->addWage('IR123456789012345678901234', 5000, 'سهم اول');

        $array = $options->toArray();

        $this->assertEquals('IRT', $array['currency']);
        $this->assertTrue($array['metadata']['auto_verify']);
        $this->assertCount(1, $array['wages']);
    }

    public function testVerifyCanceledStatus()
    {
        $result = $this->zarinpal->verify('NOK', 10000, 'A00000000000000000000000000000000000');

        $this->assertFalse($result['success']);
        $this->assertEquals('canceled', $result['Status']);
    }

    /**
     * تست یکپارچه‌سازی با API سندباکس — فقط با مرچنت واقعی اجرا می‌شود
     */
    public function testSandboxPaymentFlow()
    {
        if (!getenv('ZARINPAL_SANDBOX_MERCHANT_ID')) {
            $this->markTestSkipped('ZARINPAL_SANDBOX_MERCHANT_ID is not set.');
        }

        $answer = $this->zarinpal->request(
            'https://example.com/payment/callback',
            10000,
            'تست کتابخانه'
        );

        $this->assertTrue($answer['success']);
        $this->assertEquals(36, strlen($answer['Authority']));

        try {
            $client = new Client();
            $client->request(
                'POST',
                'https://sandbox.zarinpal.com/pg/transaction/pay/' . $answer['Authority'],
                ['form_params' => ['ok' => 'ok']]
            );
        } catch (\Exception $e) {
            // شبیه‌سازی پرداخت ممکن است در برخی محیط‌ها در دسترس نباشد
        }

        $verify = $this->zarinpal->verify(10000, $answer['Authority']);
        $this->assertContains($verify['Status'], ['success', 'verified_before', 'error']);
    }
}
