<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Widget;

use Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer;
use Spryker\Yves\Kernel\Widget\AbstractWidget;

/**
 * @method \Mollie\Yves\Mollie\MollieFactory getFactory()
 */
class MollieSavedCardsMenuItemWidget extends AbstractWidget
{
    /**
     * @var string
     */
    public const PAGE_KEY_SAVED_CARDS = 'mollieSavedCards';

    /**
     * @var string
     */
    protected const PARAMETER_IS_VISIBLE = 'isVisible';

    /**
     * @var string
     */
    protected const PARAMETER_IS_ACTIVE_PAGE = 'isActivePage';

    /**
     * @param string $activePage
     */
    public function __construct(string $activePage)
    {
        $this->addParameter(static::PARAMETER_IS_VISIBLE, $this->isSingleClickPaymentEnabled());
        $this->addParameter(static::PARAMETER_IS_ACTIVE_PAGE, $activePage === static::PAGE_KEY_SAVED_CARDS);
    }

    /**
     * @api
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'MollieSavedCardsMenuItemWidget';
    }

    /**
     * @api
     *
     * @return string
     */
    public static function getTemplate(): string
    {
        return '@Mollie/views/mollie-saved-cards-menu-item/mollie-saved-cards-menu-item.twig';
    }

    /**
     * @return bool
     */
    protected function isSingleClickPaymentEnabled(): bool
    {
        return (bool)$this->getFactory()
            ->getMollieApiClient()
            ->getSingleClickPaymentData(new MollieSingleClickPaymentDataRequestTransfer())
            ->getIsEnabled();
    }
}
