<?php

namespace MollieTest\Yves\Mollie\PaymentPage\Form\DataProvider;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieCreditCardSubFormDataProvider;

class MollieCreditCardSubFormDataProviderTest extends Unit
{
    /**
     * @return void
     */
    public function testGetDataReturnsTheSameTransferObject(): void
    {
        $mollieCreditCardSubFormDataProvider = new MollieCreditCardSubFormDataProvider();
        $quoteTransfer = new QuoteTransfer();

        $result = $mollieCreditCardSubFormDataProvider->getData($quoteTransfer);

        $this->assertSame($quoteTransfer, $result);
    }

    /**
     * @return void
     */
    public function testGetOptionsReturnsArray(): void
    {
        $mollieCreditCardSubFormDataProvider = new MollieCreditCardSubFormDataProvider();
        $quoteTransfer = new QuoteTransfer();

        $result = $mollieCreditCardSubFormDataProvider->getOptions($quoteTransfer);

        $this->assertIsArray($result);
    }
}
