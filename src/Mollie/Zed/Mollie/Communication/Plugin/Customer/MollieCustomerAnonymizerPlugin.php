<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Plugin\Customer;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Zed\Customer\Dependency\Plugin\CustomerAnonymizerPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 * @method \Mollie\Zed\Mollie\MollieConfig getConfig()
 */
class MollieCustomerAnonymizerPlugin extends AbstractPlugin implements CustomerAnonymizerPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return void
     */
    public function process(CustomerTransfer $customerTransfer): void
    {
        $this->getFacade()->deleteMollieCustomer($customerTransfer);
    }
}
