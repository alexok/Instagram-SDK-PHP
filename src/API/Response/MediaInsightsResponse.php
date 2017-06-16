<?php
/**
 * Created by PhpStorm.
 * User: alexok
 * Date: 16.06.17
 * Time: 17:31
 */

namespace Instagram\API\Response;


class MediaInsightsResponse extends BaseResponse
{
    public $media_organic_insights;

    public function getData()
    {
        return $this->media_organic_insights;
    }
}
