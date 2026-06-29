<?php

namespace Zarinpal\Tests;

use PHPUnit\Framework\TestCase;
use Zarinpal\ErrorCodes;
use Zarinpal\PaymentRequestOptions;

class UnitTest extends TestCase
{
    public function testCartDataBuilder()
    {
        $options = new PaymentRequestOptions();
        $options->buildCartData(
            [['item_name' => 'محصول', 'item_amount' => 1000, 'item_count' => 1, 'item_amount_sum' => 1000]],
            ['tax' => 100],
            ['discount' => 50]
        );

        $data = $options->toArray();

        $this->assertArrayHasKey('cart_data', $data);
        $this->assertCount(1, $data['cart_data']['items']);
        $this->assertEquals(100, $data['cart_data']['added_costs']['tax']);
    }

    public function testErrorCodeExtraction()
    {
        $response = [
            'data' => ['code' => 100, 'authority' => 'A123'],
            'errors' => [],
        ];
        $this->assertEquals(100, ErrorCodes::extractCode($response));

        $errorResponse = [
            'message' => 'Invalid authority',
            'errors' => ['authority' => ['Invalid authority.', '-54']],
        ];
        $this->assertEquals(-54, ErrorCodes::extractCode($errorResponse));
    }
}
