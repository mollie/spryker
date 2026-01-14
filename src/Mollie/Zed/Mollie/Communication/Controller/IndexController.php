<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Zed\Mollie\Communication\MollieCommunicationFactory getFactory()
 */
class IndexController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function indexAction(Request $request): array
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
}
