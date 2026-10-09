<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Mapper;

use Generated\Shared\Transfer\MollieLinksTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;

class PaymentLinkMapper implements PaymentLinkMapperInterface
{
    protected const string KEY_RESOURCE = 'resource';

    protected const string KEY_ID = 'id';

    protected const string KEY_DESCRIPTION = 'description';

    protected const string KEY_MODE = 'mode';

    protected const string KEY_ARCHIVED = 'archived';

    protected const string KEY_REUSABLE = 'reusable';

    protected const string KEY_PROFILE_ID = 'profileId';

    protected const string KEY_WEBHOOK_URL = 'webhookUrl';

    protected const string KEY_REDIRECT_URL = 'redirectUrl';

    protected const string KEY_CREATED_AT = 'createdAt';

    protected const string KEY_PAID_AT = 'paidAt';

    protected const string KEY_EXPIRES_AT = 'expiresAt';

    protected const string KEY_ALLOWED_METHODS = 'allowedMethods';

    protected const string KEY_SEQUENCE_TYPE = 'sequenceType';

    protected const string KEY_LINKS = '_links';

    /**
     * @param array<string, mixed> $paymentLinkPayload
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    public function mapPayloadToMolliePaymentLinkTransfer(array $paymentLinkPayload): MolliePaymentLinkTransfer
    {
        $mollieLinksTransfer = new MollieLinksTransfer();
        $mollieLinksTransfer->fromArray($paymentLinkPayload[static::KEY_LINKS] ?? [], true);

        $molliePaymentLinkTransfer = new MolliePaymentLinkTransfer();
        $molliePaymentLinkTransfer
            ->setResource($paymentLinkPayload[static::KEY_RESOURCE] ?? null)
            ->setId($paymentLinkPayload[static::KEY_ID] ?? null)
            ->setDescription($paymentLinkPayload[static::KEY_DESCRIPTION] ?? null)
            ->setMode($paymentLinkPayload[static::KEY_MODE] ?? null)
            ->setArchived($paymentLinkPayload[static::KEY_ARCHIVED] ?? null)
            ->setReusable($paymentLinkPayload[static::KEY_REUSABLE] ?? null)
            ->setProfileId($paymentLinkPayload[static::KEY_PROFILE_ID] ?? null)
            ->setWebhookUrl($paymentLinkPayload[static::KEY_WEBHOOK_URL] ?? null)
            ->setRedirectUrl($paymentLinkPayload[static::KEY_REDIRECT_URL] ?? null)
            ->setCreatedAt($paymentLinkPayload[static::KEY_CREATED_AT] ?? null)
            ->setPaidAt($paymentLinkPayload[static::KEY_PAID_AT] ?? null)
            ->setExpiresAt($paymentLinkPayload[static::KEY_EXPIRES_AT] ?? null)
            ->setAllowedMethods($paymentLinkPayload[static::KEY_ALLOWED_METHODS] ?? [])
            ->setSequenceType($paymentLinkPayload[static::KEY_SEQUENCE_TYPE] ?? null)
            ->setLinks($mollieLinksTransfer);

        return $molliePaymentLinkTransfer;
    }
}
