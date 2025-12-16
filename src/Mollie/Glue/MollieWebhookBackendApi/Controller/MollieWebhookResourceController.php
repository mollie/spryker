<?php

namespace Mollie\Glue\MollieWebhookBackendApi\Controller;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\Kernel\Backend\Controller\AbstractBackendApiController;

/**
 * @method \Mollie\Glue\MollieWebhookBackendApi\MollieWebhookBackendApiFactory getFactory()
 */
class MollieWebhookResourceController extends AbstractBackendApiController
{
    /**
     * @Glue({
     *        "post": {
     *             "summary": [
     *                 "Calling endpoint which returns payment info and updates OMS with new status."
     *             ],
     *             "responseAttributesClassName": "Generated\\Shared\\Transfer\\MollieApiResponseTransfer",
     *             "requestAttributesClassName": !"Generated\\Shared\\Transfer\\MollieApiRequestTransfer",
     *             "responses": {
     *                 "400": "Bad request",
     *                 "200": "OK"
     *             }
     *        }
     *    })
     *
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function postWebhookAction(
        GlueRequestTransfer $glueRequestTransfer,
    ): GlueResponseTransfer {
        return $this->getFactory()
            ->createWebhookProcessor()
            ->processWebhook($glueRequestTransfer);
    }
}
