<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Zed\Mollie\Communication\MollieCommunicationFactory getFactory()
 */
class ApiKeyController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function indexAction(Request $request): array
    {
        $dataProvider = $this->getFactory()->createApiKeyFormDataProvider();
        $form = $this->getFactory()->createApiKeyForm(
            $dataProvider->getData(),
            $dataProvider->getOptions(),
        );

        if ($form->isSubmitted() && $form->isvalid()) {
            $form->handleRequest($request);

            return [];
        }

        return $this->viewResponse([
            'apiKeyForm' => $form->createView(),
        ]);
    }
}
