<?php
/**
 * Created by PhpStorm.
 * User: alexok
 * Date: 01.08.17
 * Time: 17:44
 */

namespace Instagram\API\Request;


use Instagram\API\DeviceConstants;
use Instagram\Instagram;

class VideoConfigureRequest extends AuthenticatedBaseRequest
{
    public function __construct(Instagram $instagram, $uploadId, $uploadResult, $captionText = null)
    {
        parent::__construct($instagram);

        $captionText = $captionText != null ? $captionText : "";

        $this->setSignedBody([
            'caption' => $captionText,
            'video' => 1,
            'video_result' => $uploadResult,
            'upload_id' => $uploadId,
            'poster_frame_index' => 0,
            'length' => 29.8,
            'audio_muted' => 'false',
            'filter_type' => 0,
            'source_type' => 4,
            '_csrftoken' => $instagram->getCSRFToken(),
            '_uid' => $instagram->getLoggedInUser()->getPk(),
            '_uuid' => $instagram->getUUID(),
            "device" => [
                "manufacturer" => DeviceConstants::MANUFACTURER,
                "model" => DeviceConstants::MODEL,
                "android_version" => DeviceConstants::ANDROID_VERSION,
                "android_release" => DeviceConstants::ANDROID_RELEASE,
            ],
            'extra' => [
                'source_width'  => 640,
                'source_height' => 640,
            ],
        ]);
    }

    public function getMethod()
    {
        return self::POST;
    }

    public function getEndpoint()
    {
        return '/v1/media/configure/';
    }

    public function getResponseObject()
    {
        // TODO: Implement getResponseObject() method.
    }

    public function parseResponse()
    {
        return false;
    }
}
