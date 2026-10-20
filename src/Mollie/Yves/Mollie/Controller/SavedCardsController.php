<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use Generated\Shared\Transfer\MollieSavedCardRemoveRequestTransfer;
use Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer;
use Mollie\Yves\Mollie\Form\SavedCard\SavedCardRemoveForm;
use Mollie\Yves\Mollie\Plugin\Router\MollieRouteProviderPlugin;
use Spryker\Yves\Kernel\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Yves\Mollie\MollieFactory getFactory()
 */
class SavedCardsController extends AbstractMollieController
{
    /**
     * @var string
     */
    protected const ROUTE_LOGIN = 'login';

    /**
     * @var string
     */
    protected const GLOSSARY_KEY_REMOVE_SUCCESS = 'mollie.customer.saved_cards.remove.success';

    /**
     * @var string
     */
    protected const GLOSSARY_KEY_REMOVE_ERROR = 'mollie.customer.saved_cards.remove.error';

    /**
     * @return \Spryker\Yves\Kernel\View\View|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(): View|RedirectResponse
    {
        $customerTransfer = $this->getFactory()->getCustomerClient()->getCustomer();

        if (!$customerTransfer || !$customerTransfer->getIdCustomer()) {
            return $this->redirectResponseInternal(static::ROUTE_LOGIN);
        }

        $mollieSingleClickPaymentDataTransfer = $this->getFactory()
            ->getMollieApiClient()
            ->getCustomerSavedCards(
                (new MollieSingleClickPaymentDataRequestTransfer())->setIdCustomer($customerTransfer->getIdCustomer()),
            );

        $savedCards = [];
        foreach ($mollieSingleClickPaymentDataTransfer->getSavedCards() as $mollieMandateTransfer) {
            $savedCards[] = [
                'card' => $mollieMandateTransfer,
                'removeForm' => $this->getFactory()
                    ->createSavedCardRemoveForm($mollieMandateTransfer->getIdOrFail())
                    ->createView(),
            ];
        }

        return $this->view(
            ['savedCards' => $savedCards],
            [],
            '@Mollie/views/saved-cards/saved-cards.twig',
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Request $request): RedirectResponse
    {
        $customerTransfer = $this->getFactory()->getCustomerClient()->getCustomer();

        if (!$customerTransfer || !$customerTransfer->getIdCustomer()) {
            return $this->redirectResponseInternal(static::ROUTE_LOGIN);
        }

        $savedCardRemoveForm = $this->getFactory()->createSavedCardRemoveForm()->handleRequest($request);

        if (!$savedCardRemoveForm->isSubmitted() || !$savedCardRemoveForm->isValid()) {
            $this->addErrorMessage(static::GLOSSARY_KEY_REMOVE_ERROR);

            return $this->redirectResponseInternal(MollieRouteProviderPlugin::ROUTE_MOLLIE_CUSTOMER_SAVED_CARDS);
        }

        $mollieSavedCardRemoveRequestTransfer = (new MollieSavedCardRemoveRequestTransfer())
            ->setIdCustomer($customerTransfer->getIdCustomer())
            ->setMandateId($savedCardRemoveForm->get(SavedCardRemoveForm::FIELD_MANDATE_ID)->getData());

        $mollieSavedCardRemoveResponseTransfer = $this->getFactory()
            ->getMollieApiClient()
            ->removeSavedCard($mollieSavedCardRemoveRequestTransfer);

        if (!$mollieSavedCardRemoveResponseTransfer->getIsSuccessful()) {
            $this->addErrorMessage(static::GLOSSARY_KEY_REMOVE_ERROR);

            return $this->redirectResponseInternal(MollieRouteProviderPlugin::ROUTE_MOLLIE_CUSTOMER_SAVED_CARDS);
        }

        $this->addSuccessMessage(static::GLOSSARY_KEY_REMOVE_SUCCESS);

        return $this->redirectResponseInternal(MollieRouteProviderPlugin::ROUTE_MOLLIE_CUSTOMER_SAVED_CARDS);
    }
}
