<?php

declare(strict_types=1);

namespace MollieTest\Zed\Mollie\Business\ExpressCheckout;

use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigTransfer;
use MollieTest\Zed\Mollie\Business\AbstractBusinessTest;

/**
 * @group MollieTest
 * @group Zed
 * @group Mollie
 * @group Business
 * @group ExpressCheckout
 * @group ExpressCheckoutFacadeTest
 */
class ExpressCheckoutFacadeTest extends AbstractBusinessTest
{
    /**
     * @var string
     */
    protected const METHOD_APPLE_PAY = 'applepay';

    /**
     * @var string
     */
    protected const METHOD_GOOGLE_PAY = 'googlepay';

    /**
     * @var string
     */
    protected const METHOD_PAYPAL = 'paypal';

    /**
     * @return void
     */
    public function testGetExpressCheckoutConfigCollectionReturnsAnEntryForEachSupportedMethod(): void
    {
        $collectionTransfer = $this->mollieFacade->getExpressCheckoutConfigCollection();

        $methods = [];
        foreach ($collectionTransfer->getConfigs() as $configTransfer) {
            $methods[] = $configTransfer->getMethod();
        }

        sort($methods);
        $this->assertSame(
            [static::METHOD_APPLE_PAY, static::METHOD_GOOGLE_PAY, static::METHOD_PAYPAL],
            $methods,
        );
    }

    /**
     * @return void
     */
    public function testSaveExpressCheckoutConfigCollectionPersistsMethodConfigThatWinsOnRead(): void
    {
        $collectionTransfer = (new MollieExpressCheckoutConfigCollectionTransfer())
            ->addConfig(
                (new MollieExpressCheckoutConfigTransfer())
                    ->setMethod(static::METHOD_GOOGLE_PAY)
                    ->setIsEnabled(true),
            );

        $this->mollieFacade->saveExpressCheckoutConfigCollection($collectionTransfer);

        $googlePayConfigTransfer = null;
        foreach ($this->mollieFacade->getExpressCheckoutConfigCollection()->getConfigs() as $configTransfer) {
            if ($configTransfer->getMethod() === static::METHOD_GOOGLE_PAY) {
                $googlePayConfigTransfer = $configTransfer;
            }
        }

        $this->assertNotNull($googlePayConfigTransfer);
        $this->assertTrue($googlePayConfigTransfer->getIsEnabled());
    }
}
