<?php
/**
 * Created by PhpStorm.
 * User: alexok
 * Date: 16.06.17
 * Time: 17:09
 */

namespace Instagram\API\Request;

use Instagram\API\Constants;
use Instagram\API\Response\MediaInsightsResponse;
use Instagram\Instagram;

class MediaInsightsRequest extends AuthenticatedBaseRequest
{
    private $mediaId;

    public function __construct(Instagram $instagram, $mediaId)
    {
        parent::__construct($instagram);

        $this->mediaId = $mediaId;
        $this->addParam("ig_sig_key_version", Constants::IG_SIGNATURE_KEY_VERSION);
    }

    public function getMethod()
    {
        return self::GET;
    }

    public function getEndpoint()
    {
        return sprintf('/v1/insights/media_organic_insights/%s/', $this->mediaId);
    }

    public function getResponseObject()
    {
        return new MediaInsightsResponse();
    }

    /**
     * @return MediaInsightsResponse
     */
    public function execute()
    {
        return parent::execute();
    }
}