<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\SingleClick;

use Generated\Shared\Transfer\MollieSingleClickConfigTransfer;

interface SingleClickConfigWriterInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieSingleClickConfigTransfer $mollieSingleClickConfigTransfer
     *
     * @return void
     */
    public function saveSingleClickConfig(MollieSingleClickConfigTransfer $mollieSingleClickConfigTransfer): void;
}
