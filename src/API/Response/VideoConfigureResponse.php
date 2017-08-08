<?php
/**
 * Created by PhpStorm.
 * User: alexok
 * Date: 08.08.17
 * Time: 17:25
 */

namespace Instagram\API\Response;

class VideoConfigureResponse extends BaseResponse
{
    public $upload_id;

    /**
     * @var Model\FeedItem
     */
    public $media;
}