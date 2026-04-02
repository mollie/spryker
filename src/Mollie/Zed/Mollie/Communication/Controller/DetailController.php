<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Zed\Mollie\Communication\MollieCommunicationFactory getFactory()
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 */
class DetailController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array<string, mixed>
     */
    public function indexAction(Request $request): array
    {
        $mollieId = $request->query->get('mollie_payment_method_id');
        $currency = $request->query->get('currency');
        $dataProvider = $this->getFactory()->createMolliePaymentMethodsDataProvider();
        $mergedPaymentMethodConfigTransfer = $dataProvider->getFormData($mollieId, $currency);

        return [
            'paymentMethodConfig' => $mergedPaymentMethodConfigTransfer,
        ];
    }
}
