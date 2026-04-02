<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Controller;

use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
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
        $mollieId = $request->query->get('mollie_payment_method_id');
        $currency = $request->query->get('currency');

        $mapper = $this->getFactory()->createMollieCommunicationMapper();
        $dataProvider = $this->getFactory()->createMolliePaymentMethodsDataProvider();

        $criteriaTransfer = $mapper->createMolliePaymentMethodConfigCriteriaTransfer($mollieId, $currency);

        $existingPaymentMethodConfigTransfer = $this->getFacade()->getPaymentMethodConfigByMollieKeyAndCurrency($criteriaTransfer);
        if (!$existingPaymentMethodConfigTransfer) {
            $existingPaymentMethodConfigTransfer = new MolliePaymentMethodConfigTransfer();
            $existingPaymentMethodConfigTransfer->setCurrencyCode($currency);
        }

        $form = $this->getFactory()->createPaymentMethodConfigForm(
            $dataProvider->getFormData($mollieId, $currency),
            $dataProvider->getFormOptions($request),
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $paymentMethodConfigTransfer = $this->getFactory()
                ->createMollieCommunicationMapper()
                ->mapFormDataToPaymentMethodConfigTransfer(
                    $form->getData(),
                    $existingPaymentMethodConfigTransfer,
                );

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
            'paymentMethodConfig' => $existingPaymentMethodConfigTransfer,
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
        $idMolliePaymentMethodConfig = (int)$request->query->get('id-mollie-payment-method-config');
        $this->getFacade()->deleteMolliePaymentConfigData($idMolliePaymentMethodConfig);

        return $this->redirectResponse(
            static::MOLLIE_LIST_PATH,
            301,
        );
    }
}
