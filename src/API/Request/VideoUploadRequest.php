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
    private $uploadUrl;

    public function __construct(Instagram $instagram, $path, $uploadId, $uploadUrl)
    {
        parent::__construct($instagram);

        $this->uploadUrl = $uploadUrl->url;

        $this->addHeader('Session-ID', $uploadId);
        $this->addHeader('job', $uploadUrl->job);

        $this->addFile('video', new RequestFile($path, "application/octet-stream", sprintf("pending_media_%s.mp4", $uploadId)));
    }

    public function getUrl()
    {
        return $this->uploadUrl;
    }

    public function getMethod()
    {
        return self::POST;
    }

    public function getEndpoint()
    {
//        return '';
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