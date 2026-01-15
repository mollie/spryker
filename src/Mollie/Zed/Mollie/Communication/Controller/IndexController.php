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
     * @return array
     */
    public function indexAction(): array
    {
        $table = $this->getFactory()->createMolliePaymentMethodsTable();

        return $this->viewResponse([
            'mollieTable' => $table->render(),
        ]);
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
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function clearCacheAction(Request $request): RedirectResponse
    {
        $this->getFactory()->getStorageClient()->delete(MollieConstants::MOLLIE_AVAILABLE_METHODS_STORAGE_KEY);

        return $this->redirectResponse($request->headers->get('referer'));
    }
}
