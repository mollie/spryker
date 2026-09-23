<?php

declare(strict_types = 1);

namespace Mollie\Yves\Mollie\Widget;

use Spryker\Yves\Kernel\Widget\AbstractWidget;

/**
 * @method \Mollie\Yves\Mollie\MollieFactory getFactory()
 * @method \Mollie\Yves\Mollie\MollieConfig getConfig()
 */
class MollieExpressCheckoutWidget extends AbstractWidget
{
    public function __construct()
    {
        $this->addParameter('expressCheckoutInitEndpoint', '/mollie/express-checkout/init')
            ->addParameter('jsSrc', $this->getConfig()->getMollieExpressCheckoutJsSrc())
            ->addParameter('locale', $this->getLocale());
    }

    /**
     * @api
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'MollieExpressCheckoutWidget';
    }

    /**
     * @api
     *
     * @return string
     */
    public static function getTemplate(): string
    {
        return '@Mollie/views/mollie-express-checkout-widget/mollie-express-checkout-widget.twig';
    }
}
