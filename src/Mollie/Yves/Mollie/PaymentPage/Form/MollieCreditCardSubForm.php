<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\PaymentPage\Form;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Mollie\Yves\Mollie\MollieConfig;
use Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MollieCreditCardSubForm extends AbstractSubFormType implements SubFormInterface
{
 /**
  * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
  *
  * @return void
  */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => MolliePaymentTransfer::class,
            ])
            ->setRequired(static::OPTIONS_FIELD_NAME);
    }

 /**
  * @return string
  */
    protected function getTemplatePath(): string
    {
        return MollieConfig::PAYMENT_METHOD_INVOICE;
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
         return MollieConfig::MOLLIE_PAYMENT_CREDIT_CARD;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_CREDIT_CARD;
    }
}
