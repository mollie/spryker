<?php


declare(strict_types = 1);

namespace MollieTest\Client\Mollie;

use Codeception\Actor;

/**
 * Inherited Methods
 *
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
 */
class MollieApiClientTester extends Actor
{
    use _generated\MollieApiClientTesterActions;

    /**
     * @var string
     */
    protected const MOLLIE_MOCKED_ENABLED_PAYMENT_METHOD_RESPONSE_PAYLOAD = '{"count":2,"_embedded":{"methods":[{"resource":"method","id":"ideal","description":"iDEAL","minimumAmount":{"value":"0.01","currency":"EUR"},"maximumAmount":{"value":"50000.00","currency":"EUR"},"image":{"size1x":"https://mollie.com/external/icons/payment-methods/ideal.png","size2x":"https://mollie.com/external/icons/payment-methods/ideal%402x.png","svg":"https://mollie.com/external/icons/payment-methods/ideal.svg"},"status":"activated","_links":{"self":{"href":"...","type":"application/hal+json"}}},{"resource":"method","id":"creditcard","description":"Credit card","minimumAmount":{"value":"0.01","currency":"EUR"},"maximumAmount":{"value":"2000.00","currency":"EUR"},"image":{"size1x":"https://mollie.com/external/icons/payment-methods/creditcard.png","size2x":"https://mollie.com/external/icons/payment-methods/creditcard%402x.png","svg":"https://mollie.com/external/icons/payment-methods/creditcard.svg"},"status":"activated","_links":{"self":{"href":"...","type":"application/hal+json"}}}]},"_links":{"self":{"href":"...","type":"application/hal+json"},"documentation":{"href":"...","type":"text/html"}}}';

    /**
     * @var string
     */
    protected const MOLLIE_MOCKED_ALL_PAYMENT_METHODS_RESPONSE_PAYLOAD = '{"_embedded":{"methods":[{"resource":"method","id":"applepay","description":"Apple Pay","minimumAmount":{"value":"0.01","currency":"EUR"},"maximumAmount":{"value":"10000.00","currency":"EUR"},"image":{"size1x":"https://www.mollie.com/external/icons/payment-methods/applepay.png","size2x":"https://www.mollie.com/external/icons/payment-methods/applepay%402x.png","svg":"https://www.mollie.com/external/icons/payment-methods/applepay.svg"},"status":"rejected","_links":{"self":{"href":"https://api.mollie.com/v2/methods/applepay","type":"application/hal+json"}}},{"resource":"method","id":"googlepay","description":"Google Pay","minimumAmount":{"value":"0.01","currency":"EUR"},"maximumAmount":{"value":"10000.00","currency":"EUR"},"image":{"size1x":"https://www.mollie.com/external/icons/payment-methods/googlepay.png","size2x":"https://www.mollie.com/external/icons/payment-methods/googlepay%402x.png","svg":"https://www.mollie.com/external/icons/payment-methods/googlepay.svg"},"status":"rejected","_links":{"self":{"href":"https://api.mollie.com/v2/methods/googlepay","type":"application/hal+json"}}},{"resource":"method","id":"ideal","description":"iDEAL","minimumAmount":{"value":"0.01","currency":"EUR"},"maximumAmount":{"value":"50000.00","currency":"EUR"},"image":{"size1x":"https://www.mollie.com/external/icons/payment-methods/ideal.png","size2x":"https://www.mollie.com/external/icons/payment-methods/ideal%402x.png","svg":"https://www.mollie.com/external/icons/payment-methods/ideal.svg"},"status":"activated","_links":{"self":{"href":"https://api.mollie.com/v2/methods/ideal","type":"application/hal+json"}}}]},"count":29,"_links":{"documentation":{"href":"https://docs.mollie.com/reference/list-all-methods","type":"text/html"},"self":{"href":"https://api.mollie.com/v2/methods/all","type":"application/hal+json"}}}';

    /**
     * @var string
     */
    protected const MOLLIE_MOCKED_PAYMENT_TRANSACTION_RESPONSE_PAYLOAD = '{"resource":"payment","id":"tr_IUDAHSMGnU6qLbRaksas","mode":"live","amount":{"value":"10.00","currency":"EUR"},"description":"Order #12345","sequenceType":"oneoff","redirectUrl":"https://webshop.example.org/order/12345/","webhookUrl":"https://webshop.example.org/payments/webhook/","metadata":{"order_id":12345},"profileId":"pfl_QkEhN94Ba","status":"open","isCancelable":false,"createdAt":"2024-03-20T09:13:37+00:00","expiresAt":"2024-03-20T09:28:37+00:00","_links":{"self":{"href":"...","type":"application/hal+json"},"checkout":{"href":"https://www.mollie.com/checkout/select-method/7UhSN1zuXS","type":"text/html"},"dashboard":{"href":"https://www.mollie.com/dashboard/org_12345678/payments/tr_5B8cwPMGnU6qLbRvo7qEZo","type":"text/html"},"documentation":{"href":"...","type":"text/html"}}}';

    /**
     * @var string
     */
    protected const MOLLIE_MOCKED_REFUND_TRANSACTION_RESPONSE_PAYLOAD = '{"resource":"refund","id":"re_yuj7TaDpm877xZQzP8ULJ","mode":"live","amount":{"value":"307.85","currency":"EUR"},"description":"Order #12345","metadata":{"order_id":12345},"status":"refunded","createdAt":"2026-01-30T09:13:37+00:00","paymentId":"tr_7FQgLEW7ECECKWStSwTLJ","settlementAmount":{"value":"307.85","currency":"EUR"},"_links":{"self":{"href":"...","type":"application/hal+json"},"payment":{"href":"...","type":"application/hal+json"},"documentation":{"href":"...","type":"text/html"}}}';

    /**
     * @var string
     */
    protected const MOLLIE_MOCKED_CREATE_CAPTURE_RESPONSE_PAYLOAD = '{"resource":"capture","id":"cpt_vytxeTZskVKR7C7WgdSP3d","mode":"live","description":"Capture for cart #12345","amount":{"currency":"EUR","value":"35.95"},"metadata":{"bookkeeping_id":12345},"status":"pending","paymentId":"tr_5B8cwPMGnU6qLbRvo7qEZo","createdAt":"2023-08-02T09:29:56+00:00","_links":{"self":{"href":"...","type":"application/hal+json"},"payment":{"href":"https://api.mollie.com/v2/payments/tr_5B8cwPMGnU6qLbRvo7qEZo","type":"application/hal+json"},"documentation":{"href":"...","type":"text/html"}}}';

    /**
     * @var string
     */
    protected const MOLLIE_MOCKED_GET_CAPTURE_RESPONSE_PAYLOAD = '{"resource":"capture","id":"cpt_vytxeTZskVKR7C7WgdSP3d","mode":"live","description":"Capture for cart #12345","amount":{"currency":"EUR","value":"35.95"},"metadata":{"bookkeeping_id":12345},"status":"pending","paymentId":"tr_5B8cwPMGnU6qLbRvo7qEZo","createdAt":"2023-08-02T09:29:56+00:00","_links":{"self":{"href":"...","type":"application/hal+json"},"payment":{"href":"https://api.mollie.com/v2/payments/tr_5B8cwPMGnU6qLbRvo7qEZo","type":"application/hal+json"},"documentation":{"href":"...","type":"text/html"}}}';

    /**
     * @return string
     */
    public function getMollieMockedEnabledPaymentMethodResponsePayload(): string
    {
        return static::MOLLIE_MOCKED_ENABLED_PAYMENT_METHOD_RESPONSE_PAYLOAD;
    }

    /**
     * @return string
     */
    public function getMollieMockedAllPaymentMethodResponsePayload(): string
    {
        return static::MOLLIE_MOCKED_ALL_PAYMENT_METHODS_RESPONSE_PAYLOAD;
    }

    /**
     * @return string
     */
    public function getMollieMockedPaymentTransactionResponsePayload(): string
    {
        return static::MOLLIE_MOCKED_PAYMENT_TRANSACTION_RESPONSE_PAYLOAD;
    }

    /**
     * @return string
     */
    public function getMollieMockedRefundTransactionResponsePayload(): string
    {
        return static::MOLLIE_MOCKED_REFUND_TRANSACTION_RESPONSE_PAYLOAD;
    }

    /**
     * @return string
     */
    public function getMollieMockedCreateCaptureResponsePayload(): string
    {
        return static::MOLLIE_MOCKED_CREATE_CAPTURE_RESPONSE_PAYLOAD;
    }

    /**
     * @return string
     */
    public function getMollieMockedGetCaptureResponsePayload(): string
    {
        return static::MOLLIE_MOCKED_GET_CAPTURE_RESPONSE_PAYLOAD;
    }
}
