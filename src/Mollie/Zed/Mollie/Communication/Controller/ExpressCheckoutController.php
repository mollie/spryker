<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Controller;

use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Zed\Mollie\Communication\MollieCommunicationFactory getFactory()
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 */
class ExpressCheckoutController extends AbstractController
{
    public const string MOLLIE_EXPRESS_CHECKOUT_PATH = '/mollie/express-checkout';

    public const string MESSAGE_SUCCESS = 'Mollie express methods configuration has been saved.';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array<string, mixed>
     */
    public function indexAction(Request $request): array|RedirectResponse
    {
        $dataProvider = $this->getFactory()->createExpressCheckoutConfigFormDataProvider();
        $expressCheckoutConfigCollectionTransfer = $dataProvider->getExpressCheckoutConfigCollection();

        $form = $this->getFactory()->createExpressCheckoutConfigForm(
            $dataProvider->getData($expressCheckoutConfigCollectionTransfer),
            $dataProvider->getOptions($expressCheckoutConfigCollectionTransfer),
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getFacade()->saveExpressCheckoutConfigCollection(
                $this->mapFormDataToCollectionTransfer($form->getData()),
            );
            $this->addSuccessMessage(static::MESSAGE_SUCCESS);

            return $this->redirectResponse(static::MOLLIE_EXPRESS_CHECKOUT_PATH);
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @param array<string, mixed> $formData
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer
     */
    protected function mapFormDataToCollectionTransfer(array $formData): MollieExpressCheckoutConfigCollectionTransfer
    {
        $collectionTransfer = new MollieExpressCheckoutConfigCollectionTransfer();
        foreach ($formData as $expressMethod => $isEnabled) {
            $collectionTransfer->addConfig(
                (new MollieExpressCheckoutConfigTransfer())
                    ->setMethod($expressMethod)
                    ->setIsEnabled((bool)$isEnabled),
            );
        }

        return $collectionTransfer;
    }
}
