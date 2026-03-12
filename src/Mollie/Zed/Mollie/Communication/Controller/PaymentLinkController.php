<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Controller;

use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Zed\Mollie\Business\MollieFacadeInterface;
use Mollie\Zed\Mollie\Communication\MollieCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method MollieCommunicationFactory getFactory()
 * @method MollieFacadeInterface getFacade()
 */
class PaymentLinkController extends AbstractController
{
    protected const MESSAGE_PAYMENT_LINK_CREATE_SUCCESS = 'Payment link has been successfully created.';
    
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array<string, mixed>
     */
    public function indexAction(Request $request): array
    {
        $table = $this->getFactory()->createPaymentLinkTable();

        return $this->viewResponse([
            'paymentLinkTable' => $table->render(),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction()
    {
        $table = $this->getFactory()->createPaymentLinkTable();

        return $this->jsonResponse($table->fetchData());
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array<string, mixed>|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request): array|RedirectResponse
    {
        $dataProvider = $this->getFactory()->createPaymentLinkFormDataProvider();
        $form = $this->getFactory()
            ->createPaymentLinkForm(
                $dataProvider->getData(),
                $dataProvider->getOptions()
            )
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $paymentLinkTransfer = new MolliePaymentLinkTransfer();
            $paymentLinkTransfer->fromArray($formData, true);
            $paymentLinkTransfer->setStatus('draft');

            $molliePaymentLinkApiResponseTransfer = $this->getFacade()->createPaymentLink($paymentLinkTransfer);

            if($molliePaymentLinkApiResponseTransfer->getIsSuccessful()) {
                $this->addSuccessMessage(static::MESSAGE_PAYMENT_LINK_CREATE_SUCCESS);
            }

            return $this->redirectResponse(static::URL_PAYMENT_LINK_LIST);
        }

        return $this->viewResponse([
            'form' => $form->createView(),
        ]);
    }
}
