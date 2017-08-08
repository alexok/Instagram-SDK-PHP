<?php
/**
 * Created by PhpStorm.
 * User: alexok
 * Date: 21.07.17
 * Time: 17:09
 */

namespace Instagram\API\Request;

use Instagram\API\Response\BaseResponse;
use Instagram\API\Response\VideoUploadGetUrlResponse;
use Instagram\API\Response\Model\FeedItem;
use Instagram\Instagram;
use Instagram\Util\Helper;

class VideoUploadGetUrlRequest extends AuthenticatedBaseRequest
{
    public function __construct(Instagram $instagram, $uploadId)
    {
        parent::__construct($instagram);

        $this->addParam('_csrftoken', $instagram->getCSRFToken());
        $this->addParam('_uuid', $instagram->getUUID());
        $this->addParam('upload_media_height', 322);
        $this->addParam('upload_media_width', 196);
        $this->addParam('upload_media_duration_ms', 8200);
        $this->addParam('upload_id', $uploadId);
        $this->addParam('media_type', FeedItem::MEDIA_TYPE_VIDEO);
    }

    public function getMethod()
    {
        return self::POST;
    }

    public function getEndpoint()
    {
        return '/v1/upload/video/';
    }

    public function getResponseObject()
    {
        return new VideoUploadGetUrlResponse();
    }
}