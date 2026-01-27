<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Controller;

use Generated\Shared\Transfer\MollieProfileTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Zed\Mollie\Communication\MollieCommunicationFactory getFactory()
 */
class ProfileController extends AbstractController 
{
    public function indexAction(Request $request): array 
    {
        $profileResponseTransfer = $this->getFactory()->getMollieClient()->getCurrentProfile();
        $profileTransfer = $profileResponseTransfer->getProfile();
        return [
            MollieProfileTransfer::ID => $profileTransfer->getId(),
            MollieProfileTransfer::STATUS => $profileTransfer->getStatus(),
        ];
    }
}