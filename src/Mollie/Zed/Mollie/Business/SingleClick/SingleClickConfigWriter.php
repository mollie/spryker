<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\SingleClick;

use Generated\Shared\Transfer\MollieSingleClickConfigTransfer;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;

class SingleClickConfigWriter implements SingleClickConfigWriterInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     */
    public function __construct(
        protected MollieEntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieSingleClickConfigTransfer $mollieSingleClickConfigTransfer
     *
     * @return void
     */
    public function saveSingleClickConfig(MollieSingleClickConfigTransfer $mollieSingleClickConfigTransfer): void
    {
        $this->entityManager->saveSingleClickConfig((bool)$mollieSingleClickConfigTransfer->getIsEnabled());
    }
}
