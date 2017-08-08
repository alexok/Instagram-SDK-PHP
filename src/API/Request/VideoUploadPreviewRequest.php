<?php
/**
 * Created by PhpStorm.
 * User: alexok
 * Date: 22.07.17
 * Time: 1:58
 */

namespace Instagram\API\Request;


use Instagram\API\Framework\RequestFile;
use Instagram\API\Response\VideoUploadPreviewResponse;
use Instagram\Instagram;
use Instagram\Util\Helper;

class VideoUploadPreviewRequest extends AuthenticatedBaseRequest
{
    public function __construct(Instagram $instagram, $uploadId, $file)
    {
        parent::__construct($instagram);

        $this->addParam('_uuid', $instagram->getUUID());
        $this->addParam('_csrftoken', $instagram->getCSRFToken());
        $this->addParam('upload_id', $uploadId);
        $this->addParam('image_compression', '{"lib_name":"jt","lib_version":"1.3.0","quality":"87"}');
        $this->addFile('photo', new RequestFile($file, "application/octet-stream", sprintf("pending_media_%s.jpg", Helper::generateUploadId())));
    }

    public function getMethod()
    {
        return self::POST;
    }

    public function getEndpoint()
    {
        return '/v1/upload/photo/';
    }

    public function getResponseObject()
    {
        return new VideoUploadPreviewResponse();
    }
}