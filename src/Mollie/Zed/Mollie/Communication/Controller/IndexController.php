<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Controller;

use Composer\InstalledVersions;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
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
        $dataProvider = $this->getFactory()->createPaymentMethodsFilterFormDataProvider();
        $form = $this->getFactory()->createPaymentMethodsFilterForm(
            $dataProvider->getData(),
            $dataProvider->getOptions(),
        );

        $form->handleRequest($request);
        $responseData = [
            'form' => $form->createView(),
            'mollieTable' => $table->render(),
            'response' => $profileResponseTransfer,
            MollieConstants::MOLLIE_QUERY_PARAMETER_SHOW_ONLY_ENABLED => $showOnlyEnabledPaymentMethods,
            'version' => InstalledVersions::getPrettyVersion('mollie/spryker-payment'),
        ];

        return $this->viewResponse($responseData);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction(Request $request): JsonResponse
    {
        $dataProvider = $this->getFactory()->createPaymentMethodsFilterFormDataProvider();
        $form = $this->getFactory()->createPaymentMethodsFilterForm(
            $dataProvider->getData(),
            $dataProvider->getOptions(),
        );

        $criteriaTransfer = $form->handleRequest($request)->getData();
        if (!$criteriaTransfer) {
            $criteriaTransfer = new MolliePaymentMethodConfigCriteriaTransfer();
        }

        $reportsTable = $this->getFactory()->createMolliePaymentMethodsTable();
        $reportsTable->setCriteria($criteriaTransfer);

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

        $expirationTransfer = $this->getFacade()->getExpirationInformation($orderId);

        return $this->viewResponse(
            [
                'shouldDisplayWarning' => $expirationTransfer->getShowWarningMessage(),
                'expirationThreshold' => $expirationTransfer->getExpiresIn(),
                'warningMessage' => $expirationTransfer->getWarningMessage(),
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
