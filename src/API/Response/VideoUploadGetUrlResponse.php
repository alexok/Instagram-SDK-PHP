<?php
/**
 * Created by PhpStorm.
 * User: alexok
 * Date: 21.07.17
 * Time: 17:30
 */

namespace Instagram\API\Response;


class VideoUploadGetUrlResponse extends BaseResponse
{
    /**
     * @var string
     */
    public $upload_id;
    public $video_upload_urls;

    public function getVideoUploadUrls()
    {
        return $this->video_upload_urls;
    }

    public function getUploadParams()
    {
        // TODO
    }
}