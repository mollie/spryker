<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Controller;

use Generated\Shared\Transfer\MollieSingleClickConfigTransfer;
use Mollie\Zed\Mollie\Communication\Form\SingleClickConfigForm;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Zed\Mollie\Communication\MollieCommunicationFactory getFactory()
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 */
class SingleClickController extends AbstractController
{
    public const string MOLLIE_SINGLE_CLICK_PATH = '/mollie/single-click';

    public const string MESSAGE_SUCCESS = 'Mollie single-click payments configuration has been saved.';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array<string, mixed>
     */
    public function indexAction(Request $request): array|RedirectResponse
    {
        $dataProvider = $this->getFactory()->createSingleClickConfigFormDataProvider();
        $form = $this->getFactory()->createSingleClickConfigForm($dataProvider->getData());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $this->getFacade()->saveSingleClickConfig(
                (new MollieSingleClickConfigTransfer())
                    ->setIsEnabled((bool)$formData[SingleClickConfigForm::FIELD_IS_ENABLED]),
            );
            $this->addSuccessMessage(static::MESSAGE_SUCCESS);

            return $this->redirectResponse(static::MOLLIE_SINGLE_CLICK_PATH);
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
