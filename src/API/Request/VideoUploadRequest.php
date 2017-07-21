<?php
/**
 * Created by PhpStorm.
 * User: alexok
 * Date: 21.07.17
 * Time: 2:17
 */

namespace Instagram\API\Request;


use Instagram\API\Framework\RequestFile;
use Instagram\API\Response\VideoUploadResponse;
use Instagram\Instagram;

class VideoUploadRequest extends AuthenticatedBaseRequest
{
    private $url;

    public function __construct(Instagram $instagram, $path, $uploadParams)
    {
        parent::__construct($instagram);

        $this->addHeader('Session-ID', $uploadParams['uploadId']);
        $this->addHeader('job', $uploadParams['job']);

        $this->addFile('video', new RequestFile($path, "application/octet-stream", sprintf("pending_media_%s.mp4", $uploadId)));
    }

    public function getMethod()
    {
        return self::POST;
    }

    public function getEndpoint()
    {
        return "/v1/upload/video/";
    }

    public function getResponseObject()
    {
        return new VideoUploadResponse();
    }

    /**
     * @return VideoUploadResponse
     */
    public function execute()
    {
        return parent::execute();
    }
}