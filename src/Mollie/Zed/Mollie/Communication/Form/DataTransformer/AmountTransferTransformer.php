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
     * @return float|null
     */
    public function transform(mixed $amountTransfer): float|null
    {
        if (!$amountTransfer) {
            return null;
        }

        return (float)$amountTransfer->getValue();
    }

    /**
     * @param mixed $value
     *
     * @return \Generated\Shared\Transfer\MollieAmountTransfer|null
     */
    public function reverseTransform(mixed $value): MollieAmountTransfer|null
    {
        return (new MollieAmountTransfer())->setValue((string)$value);
    }
}
