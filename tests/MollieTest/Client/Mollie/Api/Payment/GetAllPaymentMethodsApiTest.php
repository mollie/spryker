<?php

declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Api\Payment;

use ArrayObject;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetAllMethodsRequest;
use Mollie\Client\Mollie\MollieClientInterface;
use MollieTest\Client\Mollie\AbstractClientTest;

class GetAllPaymentMethodsApiTest extends AbstractClientTest
{
 /**
  * @return void
  */
    public function testGetAllPaymentMethodsApi(): void
    {
        $transfer = $this->createMollieApiRequestTransfer();
        $client = $this->createClient();

        $mollieAvailablePaymentMethodsApiResponseTransfer = $client->getAllPaymentMethods($transfer);
        $methods = $mollieAvailablePaymentMethodsApiResponseTransfer->getCollection()->getMethods();
        $methodIds = $this->getMethodIds($methods);
        $statuses = $this->getUniqueStatuses($methods);

        $this->assertNotEmpty($methods);
        $this->assertContains('applepay', $methodIds);
        $this->assertContains('googlepay', $methodIds);
        $this->assertContains('ideal', $methodIds);
        $this->assertContains('activated', $statuses);
        $this->assertContains('rejected', $statuses);
    }

    /**
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    protected function createMollieApiRequestTransfer(): MollieApiRequestTransfer
    {
        $transfer = new MollieApiRequestTransfer();
        $queryTransfer = new MolliePaymentMethodQueryParametersTransfer();

        $queryTransfer
            ->setLocale('en_US')
            ->setSequenceType('oneoff');

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
     * @param array $methods
     *
     * @return array
     */
    protected function getUniqueStatuses(ArrayObject $methods): array
    {
        $statuses = [];
        foreach ($methods as $method) {
            $status = $method->getStatus();
            if (isset($statuses[$status])) {
                continue;
            }

            $statuses[$status] = null;
        }

        return array_keys($statuses);
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    public function createClient(): MollieClientInterface
    {
        $mollieFactoryMock = $this->createMollieFactoryMock();
        $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($this->createMockApiClientForAllPaymentMethods());

        return $this->createClientMock($mollieFactoryMock);
    }

    /**
     * @return \Mollie\Api\Fake\MockMollieClient
     */
    public function createMockApiClientForAllPaymentMethods(): MockMollieClient
    {
        $response = [
            GetAllMethodsRequest::class => new MockResponse(
                $this->tester->getMollieMockedAllPaymentMethodResponsePayload(),
            ),
        ];

        return $this->createMockApiClient($response);
    }
}
