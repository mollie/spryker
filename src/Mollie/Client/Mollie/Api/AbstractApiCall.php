<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api;

use Exception;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\MollieConfig;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

abstract class AbstractApiCall implements ApiCallInterface
{
    /**
     * @param \Mollie\Api\MollieApiClient $mollieApiClient
     * @param \Mollie\Client\Mollie\MollieConfig $mollieConfig
     */
    public function __construct(
        protected MollieApiClient $mollieApiClient,
        protected MollieConfig $mollieConfig,
    ) {
    }

    /**
     * @param \Mollie\Api\Http\Request $request
     *
     * @return \Generated\Shared\Transfer\MollieApiResponseTransfer
     */
    //abstract protected function send(Request $request): MollieApiResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    abstract protected function formatApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer;

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function execute(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): AbstractTransfer
    {
        $mollieResponseApiTransfer = new MollieApiResponseTransfer();

        try {
            $this->mollieApiClient->setApiKey($this->mollieConfig->getMollieApiKey());
            $request = $this->buildRequest($mollieApiRequestTransfer);
            $result = $this->mollieApiClient->send($request);

            if ($result->getResponse()->status() < 200 || $result->getResponse()->status() > 300) {
                $mollieResponseApiTransfer->setIsSuccessful(false);
            }

            $mollieResponseApiTransfer
                ->setPayload(json_decode($result->getResponse()->getPsrResponse()->getBody()->getContents(), true))
                ->setIsSuccessful(true);
        } catch (Exception $e) {
            $mollieResponseApiTransfer
                ->setMessage($e->getMessage())
                ->setIsSuccessful(false);
        }

        //$mollieResponseApiTransfer = $this->send($request);

        return $this->formatApiResponse($mollieResponseApiTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    abstract protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request;
}
