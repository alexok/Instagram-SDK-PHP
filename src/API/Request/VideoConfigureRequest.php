<?php
/**
 * Created by PhpStorm.
 * User: alexok
 * Date: 01.08.17
 * Time: 17:44
 */

namespace Instagram\API\Request;


use Instagram\API\DeviceConstants;
use Instagram\API\Response\VideoConfigureResponse;
use Instagram\Instagram;

class VideoConfigureRequest extends AuthenticatedBaseRequest
{
    public function __construct(Instagram $instagram, $uploadId, $info, $captionText = null)
    {
        parent::__construct($instagram);

        $captionText = $captionText != null ? $captionText : "";

        $this->setSignedBody([
            '_csrftoken' => $instagram->getCSRFToken(),
            '_uid' => $instagram->getLoggedInUser()->getPk(),
            '_uuid' => $instagram->getUUID(),
            'caption' => $captionText,
            'video' => 1,
            'video_result' => '',
            'upload_id' => $uploadId,
            'poster_frame_index' => 0,
            'length' => $info['duration'],
            'audio_muted' => 'false',
            'filter_type' => 0,
            'source_type' => 4,
            'device' => [
                "manufacturer" => DeviceConstants::MANUFACTURER,
                "model" => DeviceConstants::MODEL,
                "android_version" => DeviceConstants::ANDROID_VERSION,
                "android_release" => DeviceConstants::ANDROID_RELEASE,
            ],
            'extra' => [
                'source_width'  => $info['width'],
                'source_height' => $info['height'],
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
        return new VideoConfigureResponse();
    }
}
