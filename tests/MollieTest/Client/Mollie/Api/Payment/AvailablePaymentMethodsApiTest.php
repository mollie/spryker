<?php

declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Api\Payment;

use ArrayObject;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetEnabledMethodsRequest;
use Mollie\Client\Mollie\MollieClientInterface;
use MollieTest\Client\Mollie\AbstractClientTest;
use MollieTest\Client\Mollie\MollieApiClientTester;

class AvailablePaymentMethodsApiTest extends AbstractClientTest
{
    /**
     * @var \MollieTest\Client\Mollie\MollieApiClientTester
     */
    protected MollieApiClientTester $tester;

    /**
     * @return void
     */
    public function testGetAvailablePaymentMethodsApi(): void
    {
        $transfer = $this->prepareTransfersForSuccessfulGetAvailablePaymentMethodsApiTest();
        $client = $this->createClient();
        $mollieAvailablePaymentMethodsApiResponseTransfer = $client->getAvailablePaymentMethods($transfer);
        $methods = $mollieAvailablePaymentMethodsApiResponseTransfer->getCollection()->getMethods();
        $methodIds = $this->getMethodIds($methods);

        $this->assertNotEmpty($methods);
        $this->assertContains('ideal', $methodIds);
        $this->assertContains('creditcard', $methodIds);
    }

    /**
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    protected function prepareTransfersForSuccessfulGetAvailablePaymentMethodsApiTest(): MollieApiRequestTransfer
    {
        $transfer = new MollieApiRequestTransfer();
        $queryTransfer = new MolliePaymentMethodQueryParametersTransfer();
        $amountTransfer = new MollieAmountTransfer();

        $currency = 'EUR';
        $value = '111';
        $amountTransfer->setCurrency($currency);
        $amountTransfer->setValue($value);

        $queryTransfer->setAmount($amountTransfer);
        $queryTransfer->setSequenceType('oneOff');
        $transfer->setMolliePaymentMethodQueryParameters($queryTransfer);

        return $transfer;
    }

    /**
     * @param \ArrayObject $methods
     *
     * @return array
     */
    protected function getMethodIds(ArrayObject $methods): array
    {
        $methodIds = [];
        foreach ($methods as $method) {
            $methodIds[] = $method->getId();
        }

        return $methodIds;
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    public function createClient(): MollieClientInterface
    {
        $mollieFactoryMock = $this->createMollieFactoryMock();
        $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($this->createMockApiClientForAvailablePaymentMethods());

        return $this->createClientMock($mollieFactoryMock);
    }

    /**
     * @return \Mollie\Api\Fake\MockMollieClient
     */
    public function createMockApiClientForAvailablePaymentMethods(): MockMollieClient
    {
        $response = [
            GetEnabledMethodsRequest::class => new MockResponse(
                $this->tester->getMollieMockedPaymentMethodResponsePayload(),
            ),
        ];

        return $this->createMockApiClient($response);
    }
}
