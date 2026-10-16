<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\SingleClick;

use Generated\Shared\Transfer\MollieCreditCardPaymentTransfer;
use Generated\Shared\Transfer\MollieCustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Shared\Mollie\MollieConfig as SharedConfig;
use Spryker\Shared\Log\LoggerTrait;

class SingleClickPaymentResolver implements SingleClickPaymentResolverInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected const LOG_MESSAGE_FORMAT = 'Mollie single-click: path=%s customer=%s reason=%s orderReference=%s';

    /**
     * @var string
     */
    protected const CUSTOMER_NONE = 'none';

    /**
     * @var string
     */
    protected const CUSTOMER_CREATED = 'created';

    /**
     * @var string
     */
    protected const CUSTOMER_REUSED = 'reused';

    /**
     * @var string
     */
    public const REASON_FEATURE_DISABLED = 'feature_disabled';

    /**
     * @var string
     */
    public const REASON_GUEST = 'guest';

    /**
     * @var string
     */
    public const REASON_NO_CONSENT = 'no_consent';

    /**
     * @var string
     */
    public const REASON_CUSTOMER_NOT_FOUND = 'customer_not_found';

    /**
     * @var string
     */
    public const REASON_MANDATE_NOT_VALID = 'mandate_not_valid';

    /**
     * @var string
     */
    public const REASON_CUSTOMER_CREATION_FAILED = 'customer_creation_failed';

    /**
     * @var string
     */
    public const REASON_ONE_CLICK_PAYMENT_FAILED = 'one_click_payment_failed';

    /**
     * @var string
     */
    protected const REASON_SAVED_CARD = 'saved_card';

    /**
     * @var string
     */
    protected const REASON_CONSENT_GIVEN = 'consent_given';

    /**
     * @param \Mollie\Zed\Mollie\Business\SingleClick\SingleClickConfigReaderInterface $singleClickConfigReader
     * @param \Mollie\Zed\Mollie\Business\SingleClick\MollieCustomerProviderInterface $mollieCustomerProvider
     * @param \Mollie\Zed\Mollie\Business\SingleClick\SavedCardReaderInterface $savedCardReader
     */
    public function __construct(
        protected SingleClickConfigReaderInterface $singleClickConfigReader,
        protected MollieCustomerProviderInterface $mollieCustomerProvider,
        protected SavedCardReaderInterface $savedCardReader,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $orderReference
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function resolve(QuoteTransfer $quoteTransfer, string $orderReference): QuoteTransfer
    {
        $paymentTransfer = $quoteTransfer->getPayment();
        if (!$paymentTransfer || $paymentTransfer->getPaymentMethod() !== SharedConfig::MOLLIE_PAYMENT_CREDIT_CARD) {
            return $quoteTransfer;
        }

        $creditCardPaymentTransfer = $paymentTransfer->getMollieCreditCardPayment() ?? new MollieCreditCardPaymentTransfer();
        $paymentTransfer->setMollieCreditCardPayment($creditCardPaymentTransfer);
        $isSavedCardChosen = (bool)$creditCardPaymentTransfer->getMandateId();

        if (!$this->singleClickConfigReader->isSingleClickPaymentEnabled()) {
            return $this->applyFullCheckout($quoteTransfer, $orderReference, static::REASON_FEATURE_DISABLED, $isSavedCardChosen);
        }

        $customerTransfer = $quoteTransfer->getCustomer();
        if (!$customerTransfer || $customerTransfer->getIsGuest() || !$customerTransfer->getIdCustomer()) {
            return $this->applyFullCheckout($quoteTransfer, $orderReference, static::REASON_GUEST, $isSavedCardChosen);
        }

        if ($isSavedCardChosen) {
            return $this->resolveSavedCard($quoteTransfer, $orderReference);
        }

        if (!$creditCardPaymentTransfer->getIsSaveCardConsentGiven()) {
            return $this->applyFullCheckout($quoteTransfer, $orderReference, static::REASON_NO_CONSENT, false);
        }

        return $this->resolveSaveCard($quoteTransfer, $orderReference);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function isOneClickPayment(QuoteTransfer $quoteTransfer): bool
    {
        $checkoutPath = $quoteTransfer->getPayment()?->getMollieCreditCardPayment()?->getCheckoutPath();

        return $checkoutPath === SharedConfig::SINGLE_CLICK_CHECKOUT_PATH_ONE_CLICK;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $orderReference
     * @param string $reason
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function applyFallback(QuoteTransfer $quoteTransfer, string $orderReference, string $reason): QuoteTransfer
    {
        $this->getCreditCardPayment($quoteTransfer)
            ->setMandateId(null)
            ->setMollieCustomerId(null)
            ->setIsStoreCredentials(false)
            ->setCardToken(null)
            ->setCheckoutPath(SharedConfig::SINGLE_CLICK_CHECKOUT_PATH_FULL_FALLBACK);

        $this->getLogger()->warning(
            sprintf(static::LOG_MESSAGE_FORMAT, SharedConfig::SINGLE_CLICK_CHECKOUT_PATH_FULL_FALLBACK, static::CUSTOMER_NONE, $reason, $orderReference),
            ['idCustomer' => $quoteTransfer->getCustomer()?->getIdCustomer()],
        );

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $orderReference
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function resolveSavedCard(QuoteTransfer $quoteTransfer, string $orderReference): QuoteTransfer
    {
        $creditCardPaymentTransfer = $this->getCreditCardPayment($quoteTransfer);
        $mollieCustomerTransfer = $this->mollieCustomerProvider->findMollieCustomer($quoteTransfer->getCustomerOrFail());

        if (!$mollieCustomerTransfer) {
            return $this->applyFallback($quoteTransfer, $orderReference, static::REASON_CUSTOMER_NOT_FOUND);
        }

        if (!$this->isValidMandateOfCustomer($creditCardPaymentTransfer->getMandateIdOrFail(), $mollieCustomerTransfer)) {
            return $this->applyFallback($quoteTransfer, $orderReference, static::REASON_MANDATE_NOT_VALID);
        }

        $creditCardPaymentTransfer
            ->setMollieCustomerId($mollieCustomerTransfer->getMollieCustomerId())
            ->setIsStoreCredentials(false)
            ->setCardToken(null)
            ->setCheckoutPath(SharedConfig::SINGLE_CLICK_CHECKOUT_PATH_ONE_CLICK);

        $this->logPath($quoteTransfer, $orderReference, static::CUSTOMER_REUSED, static::REASON_SAVED_CARD);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $orderReference
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function resolveSaveCard(QuoteTransfer $quoteTransfer, string $orderReference): QuoteTransfer
    {
        $mollieCustomerTransfer = $this->mollieCustomerProvider->getOrCreateMollieCustomer($quoteTransfer);

        if (!$mollieCustomerTransfer) {
            return $this->applyFullCheckout($quoteTransfer, $orderReference, static::REASON_CUSTOMER_CREATION_FAILED, false);
        }

        $this->getCreditCardPayment($quoteTransfer)
            ->setMandateId(null)
            ->setMollieCustomerId($mollieCustomerTransfer->getMollieCustomerId())
            ->setIsStoreCredentials(true)
            ->setCheckoutPath(SharedConfig::SINGLE_CLICK_CHECKOUT_PATH_SAVE_CARD);

        $customerState = $mollieCustomerTransfer->getIsNew() ? static::CUSTOMER_CREATED : static::CUSTOMER_REUSED;
        $this->logPath($quoteTransfer, $orderReference, $customerState, static::REASON_CONSENT_GIVEN);

        return $quoteTransfer;
    }

    /**
     * A saved card that can't be used has no card token to fall back to, so it falls back to the Mollie payment page.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $orderReference
     * @param string $reason
     * @param bool $isSavedCardChosen
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function applyFullCheckout(
        QuoteTransfer $quoteTransfer,
        string $orderReference,
        string $reason,
        bool $isSavedCardChosen,
    ): QuoteTransfer {
        if ($isSavedCardChosen) {
            return $this->applyFallback($quoteTransfer, $orderReference, $reason);
        }

        $this->getCreditCardPayment($quoteTransfer)
            ->setMandateId(null)
            ->setMollieCustomerId(null)
            ->setIsStoreCredentials(false)
            ->setCheckoutPath(SharedConfig::SINGLE_CLICK_CHECKOUT_PATH_FULL);

        $this->logPath($quoteTransfer, $orderReference, static::CUSTOMER_NONE, $reason);

        return $quoteTransfer;
    }

    /**
     * @param string $mandateId
     * @param \Generated\Shared\Transfer\MollieCustomerTransfer $mollieCustomerTransfer
     *
     * @return bool
     */
    protected function isValidMandateOfCustomer(string $mandateId, MollieCustomerTransfer $mollieCustomerTransfer): bool
    {
        foreach ($this->savedCardReader->getSavedCards($mollieCustomerTransfer) as $mollieMandateTransfer) {
            if ($mollieMandateTransfer->getId() === $mandateId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\MollieCreditCardPaymentTransfer
     */
    protected function getCreditCardPayment(QuoteTransfer $quoteTransfer): MollieCreditCardPaymentTransfer
    {
        return $quoteTransfer->getPaymentOrFail()->getMollieCreditCardPaymentOrFail();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $orderReference
     * @param string $customerState
     * @param string $reason
     *
     * @return void
     */
    protected function logPath(QuoteTransfer $quoteTransfer, string $orderReference, string $customerState, string $reason): void
    {
        $creditCardPaymentTransfer = $this->getCreditCardPayment($quoteTransfer);

        $this->getLogger()->info(
            sprintf(static::LOG_MESSAGE_FORMAT, $creditCardPaymentTransfer->getCheckoutPath(), $customerState, $reason, $orderReference),
            [
                'idCustomer' => $quoteTransfer->getCustomer()?->getIdCustomer(),
                'mollieCustomerId' => $creditCardPaymentTransfer->getMollieCustomerId(),
                'mandateId' => $creditCardPaymentTransfer->getMandateId(),
            ],
        );
    }
}
