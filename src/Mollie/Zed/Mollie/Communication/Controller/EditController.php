<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Controller;

use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Zed\Mollie\Communication\MollieCommunicationFactory getFactory()
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 */
class EditController extends AbstractController
{
    public const string MOLLIE_VIEW_PATH_WITH_PARAMS = '/mollie/detail?mollie_payment_method_id=%s&currency=%s';

    public const string MOLLIE_LIST_PATH = '/mollie';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array<string, mixed>
     */
    public function indexAction(Request $request): array|RedirectResponse
    {
        $mollieId = $request->query->get(MollieConstants::QUERY_MOLLIE_PAYMENT_METHOD_ID);
        $currency = $request->query->get(MollieConstants::QUERY_CURRENCY);
        $dataProvider = $this->getFactory()->createMolliePaymentMethodsDataProvider();

        $form = $this->getFactory()->createPaymentMethodConfigForm(
            $dataProvider->getFormData($mollieId, $currency),
            $dataProvider->getFormOptions($request),
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $paymentMethodConfigTransfer = $form->getData();
            $paymentMethodConfigTransfer->setCurrencyCode($currency);
            $this->getFacade()->writeMolliePaymentConfigData($paymentMethodConfigTransfer);

            return $this->redirectResponse(
                sprintf(
                    static::MOLLIE_VIEW_PATH_WITH_PARAMS,
                    $mollieId,
                    $currency,
                ),
                301,
            );
        }

        return [
            'currency' => $currency,
            'mollieId' => $mollieId,
            'form' => $form->createView(),
        ];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function restoreDefaultsAction(Request $request): RedirectResponse
    {
        $idMolliePaymentMethodConfig = (int)$request->query->get(MollieConstants::QUERY_MOLLIE_PAYMENT_METHOD_CONFIG_ID);
        $this->getFacade()->deleteMolliePaymentConfigData($idMolliePaymentMethodConfig);

        return $this->redirectResponse(
            static::MOLLIE_LIST_PATH,
            301,
        );
    }
}
