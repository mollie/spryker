<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Controller;

use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Zed\Mollie\Communication\MollieCommunicationFactory getFactory()
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 */
class IndexController extends AbstractController
{
    public const string CAPTURE_EXPIRATION_WARNING_MESSAGE = 'mollie.order.warning.capture-expiration';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array<string, mixed>
     */
    public function indexAction(Request $request): array
    {
        $profileResponseTransfer = $this->getFactory()->getMollieClient()->getCurrentProfile();
        $showOnlyEnabledPaymentMethods = $request->query->get(MollieConstants::MOLLIE_QUERY_PARAMETER_SHOW_ONLY_ENABLED);
        $table = $this->getFactory()->createMolliePaymentMethodsTable();
        $responseData = [
            'mollieTable' => $table->render(),
            'response' => $profileResponseTransfer,
            MollieConstants::MOLLIE_QUERY_PARAMETER_SHOW_ONLY_ENABLED => $showOnlyEnabledPaymentMethods,
        ];

        return $this->viewResponse($responseData);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction(): JsonResponse
    {
        $reportsTable = $this->getFactory()->createMolliePaymentMethodsTable();

        return $this->jsonResponse(
            $reportsTable->fetchData(),
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array<string, mixed>
     */
    public function expirationWarningAction(Request $request): array
    {
        $orderId = (int)$request->query->get('id-sales-order');
        if (!$orderId) {
            return $this->viewResponse(['shouldDisplayWarning' => false]);
        }

        $expirationThreshold = $this->getFactory()->getConfig()->getExpirationWarningThreshold();

        $shouldDisplayWarning = $this->getFacade()->shouldDisplayExpirationWarning($orderId);

        return $this->viewResponse(
            [
                'shouldDisplayWarning' => $shouldDisplayWarning,
                'expirationThreshold' => $expirationThreshold,
                'warningMessage' => static::CAPTURE_EXPIRATION_WARNING_MESSAGE,
            ],
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function clearCacheAction(Request $request): RedirectResponse
    {
        $this->getFactory()->createMollieCacheInvalidator()->invalidateCache();

        return $this->redirectResponse($request->headers->get('referer'));
    }
}
