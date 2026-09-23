<?php


declare(strict_types=1);

namespace MollieTest\Zed\Mollie\Business\ExpressCheckout;

use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer;
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
        $collectionTransfer = $this->mollieFacade->getExpressCheckoutConfigCollection(
            new MollieExpressCheckoutConfigCriteriaTransfer(),
        );

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
    public function testGetExpressCheckoutConfigCollectionFiltersByExpressMethodWhenCriteriaIsSet(): void
    {
        $criteriaTransfer = (new MollieExpressCheckoutConfigCriteriaTransfer())
            ->setExpressMethod(static::METHOD_PAYPAL);

        $collectionTransfer = $this->mollieFacade->getExpressCheckoutConfigCollection($criteriaTransfer);

        $methods = [];
        foreach ($collectionTransfer->getConfigs() as $configTransfer) {
            $methods[] = $configTransfer->getMethod();
        }

        $this->assertSame([static::METHOD_PAYPAL], $methods);
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

        $googlePayConfigTransfer = $this->getGooglePayConfigTransfer();
        $this->assertNotNull($googlePayConfigTransfer);
        $this->assertTrue($googlePayConfigTransfer->getIsEnabled());
    }

    /**
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutConfigTransfer|null
     */
    protected function getGooglePayConfigTransfer(): ?MollieExpressCheckoutConfigTransfer
    {
        $criteriaTransfer = (new MollieExpressCheckoutConfigCriteriaTransfer())
            ->setExpressMethod(static::METHOD_GOOGLE_PAY);

        $configTransfers = $this->mollieFacade
            ->getExpressCheckoutConfigCollection($criteriaTransfer)
            ->getConfigs();

        return $configTransfers->count() > 0 ? $configTransfers[0] : null;
    }
}
