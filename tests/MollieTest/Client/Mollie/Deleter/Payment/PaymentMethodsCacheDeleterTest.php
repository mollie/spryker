<?php


declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Deleter\Payment;

use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Mollie\Shared\Mollie\MollieConstants;
use MollieTest\Client\Mollie\AbstractClientTest;

class PaymentMethodsCacheDeleterTest extends AbstractClientTest
{
    /**
     * @return void
     */
    public function testDeleteMultipleEnabledPaymentMethodsRespectsScanLimit(): void
    {
        // Arrange
        $parameters = (new MolliePaymentMethodQueryParametersTransfer())
            ->setLocale('en_US')
            ->setIncludeIssuers(true)
            ->setSequenceType(MollieConstants::MOLLIE_SEQUENCE_TYPE_ONE_OFF);

        // Get storage client
        $factory = $this->createMollieFactoryMock();
        $keyGenerator = $factory->createPaymentMethodsCacheKeyGenerator();
        $cacheKeyPrefix = $factory->getConfig()->getCacheKeyPrefixForEnabledPaymentMethods();
        $cacheKey = $keyGenerator->generateCacheKey($parameters, $cacheKeyPrefix);
        $this->tester->getStorageClient()->set($cacheKey, $this->tester->getMollieMockedEnabledPaymentMethodResponsePayload());

        $client = $this->createClientMock($factory);
        $client->deleteEnabledPaymentMethodsCache($parameters);

        $this->tester->assertStorageNotHasKey($cacheKey);
    }

    /**
     * @return \Mollie\Client\Mollie\Generator\Payment\PaymentMethodsCacheKeyGeneratorInterface
     */
//    protected function createCacheKeyGeneratorMock(): PaymentMethodsCacheKeyGeneratorInterface
//    {
//         $paymentMethodsCacheKeyGenerator = $this->getMockBuilder(PaymentMethodsCacheKeyGenerator::class)
//            ->onlyMethods(['generateCacheKey'])
//            ->getMock();
//
//         $paymentMethodsCacheKeyGenerator->method('generateCacheKey')->willReturn('mollie:enabled:payment_methods');
//
//         return $paymentMethodsCacheKeyGenerator;
//    }
}
