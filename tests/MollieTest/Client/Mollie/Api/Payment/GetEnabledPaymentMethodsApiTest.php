<?php

declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Api\Payment;

use ArrayObject;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetEnabledMethodsRequest;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\MollieClient;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Client\Mollie\MollieDependencyProvider;
use Mollie\Client\Mollie\MollieFactory;
use MollieTest\Client\Mollie\AbstractClientTest;
use Spryker\Client\Kernel\Container;

class GetEnabledPaymentMethodsApiTest extends AbstractClientTest
{
 /**
  * @return void
  */
    public function testGetEnabledPaymentMethodsApi(): void
    {
        $transfer = $this->createMollieApiRequestTransfer();
        $client = $this->createClient();
        $mollieAvailablePaymentMethodsApiResponseTransfer = $client->getEnabledPaymentMethods($transfer);
        $methods = $mollieAvailablePaymentMethodsApiResponseTransfer->getCollection()->getMethods();
        $methodIds = $this->getMethodIds($methods);

        $this->assertNotEmpty($methods);
        $this->assertContains('ideal', $methodIds);
        $this->assertContains('creditcard', $methodIds);
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
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    public function createClient(): MollieClientInterface
    {
        $mollieFactoryMock = $this->createMollieFactoryMock();
        $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($this->createMockApiClientForEnabledPaymentMethods());

        return $this->createClientMock($mollieFactoryMock);
    }

     /**
      * @return \Mollie\Api\Fake\MockMollieClient
      */
    public function createMockApiClientForEnabledPaymentMethods(): MockMollieClient
    {
        $response = [
            GetEnabledMethodsRequest::class => new MockResponse(
                $this->tester->getMollieMockedEnabledPaymentMethodResponsePayload(),
            ),
        ];

        return $this->createMockApiClient($response);
    }
}
