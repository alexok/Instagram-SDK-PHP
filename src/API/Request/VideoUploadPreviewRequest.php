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

class VideoUploadPreviewRequest extends AuthenticatedBaseRequest
{
    public function __construct(Instagram $instagram, $photoData)
    {
        parent::__construct($instagram);
        $this->addFileData('tmb', $photoData);
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