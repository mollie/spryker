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
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array<string, mixed>
     */
    public function indexAction(Request $request): array|RedirectResponse
    {
        $mollieId = $request->query->get('mollie_payment_method_id');
        $dataProvider = $this->getFactory()->createMolliePaymentMethodsDataProvider();

        $existingPaymentMethodConfigTransfer = $this->getFacade()->getPaymentMethodConfigByMollieKey($mollieId);
        if (!$existingPaymentMethodConfigTransfer) {
            $existingPaymentMethodConfigTransfer = new MolliePaymentMethodConfigTransfer();
        }

        $form = $this->getFactory()->createPaymentMethodConfigForm(
            $dataProvider->getFormData($mollieId),
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
                '/mollie/detail?mollie_payment_method_id=' . $mollieId,
                301,
            );
        }

        $form = $this->getFactory()->createPaymentMethodConfigForm(
            $dataProvider->getFormData($mollieId),
        );

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
        $mollieId = (int)$request->query->get('id-mollie-payment-method-config');
        $this->getFacade()->deleteMolliePaymentConfigData($mollieId);

        return $this->redirectResponse(
            '/mollie',
            301,
        );
    }
}
