<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Form\DataTransformer;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<\Generated\Shared\Transfer\MollieAmountTransfer|null, float|null>
 */
class AmountTransferTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $amountTransfer
     *
     * @return float
     */
    public function transform(mixed $amountTransfer): float
    {
        return (float)$amountTransfer->getValue();
    }

    /**
     * @param mixed $value
     *
     * @return \Generated\Shared\Transfer\MollieAmountTransfer
     */
    public function reverseTransform(mixed $value): MollieAmountTransfer
    {
        return (new MollieAmountTransfer())->setValue((string)$value);
    }
}
