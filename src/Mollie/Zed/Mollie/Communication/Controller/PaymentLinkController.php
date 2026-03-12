<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Controller;

use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Zed\Mollie\Communication\MollieCommunicationFactory getFactory()
 * @method \Mollie\Zed\Mollie\Communication\Controller\MollieFacadeInterface getFacade()
 */
class PaymentLinkController extends AbstractController
{
    /**
     * @var string
     */
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
    public function tableAction(): JsonResponse
    {
        $table = $this->getFactory()->createPaymentLinkTable();

        return $this->jsonResponse($table->fetchData());
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array<string, mixed>
     */
    public function createAction(Request $request): array|RedirectResponse
    {
        $dataProvider = $this->getFactory()->createPaymentLinkFormDataProvider();
        $form = $this->getFactory()
            ->createPaymentLinkForm(
                $dataProvider->getData(),
                $dataProvider->getOptions(),
            )
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $paymentLinkTransfer = new MolliePaymentLinkTransfer();
            $paymentLinkTransfer->fromArray($formData, true);
            $paymentLinkTransfer->setStatus('draft');

            $molliePaymentLinkApiResponseTransfer = $this->getFacade()->createPaymentLink($paymentLinkTransfer);

            if ($molliePaymentLinkApiResponseTransfer->getIsSuccessful()) {
                $this->addSuccessMessage(static::MESSAGE_PAYMENT_LINK_CREATE_SUCCESS);
            }

            return $this->redirectResponse(static::URL_PAYMENT_LINK_LIST);
        }

        return $this->viewResponse([
            'form' => $form->createView(),
        ]);
    }
}
