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
 */
class IndexController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array<string, mixed>
     */
    public function indexAction(Request $request): array
    {
        $profileResponseTransfer = $this->getFactory()->getMollieClient()->getCurrentProfile();
        $isProfileCallSuccessful = $profileResponseTransfer->getIsSuccessful();
        $showOnlyEnabledPaymentMethods = $request->query->get(MollieConstants::MOLLIE_QUERY_PARAMETER_SHOW_ONLY_ENABLED);
        $table = $this->getFactory()->createMolliePaymentMethodsTable();
        $responseData = [
            'mollieTable' => $table->render(),
            'isProfileCallSuccessful' => $isProfileCallSuccessful,
            MollieConstants::MOLLIE_QUERY_PARAMETER_SHOW_ONLY_ENABLED => $showOnlyEnabledPaymentMethods,
        ];

        if ($isProfileCallSuccessful) {
            $responseData['profile'] = $profileResponseTransfer->getProfile();
        }

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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function clearCacheAction(Request $request): RedirectResponse
    {
        $this->getFactory()->createMollieCacheInvalidator()->invalidateCache();

        return $this->redirectResponse($request->headers->get('referer'));
    }
}
