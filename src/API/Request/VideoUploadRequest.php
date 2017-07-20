<?php
/**
 * Created by PhpStorm.
 * User: alexok
 * Date: 21.07.17
 * Time: 2:17
 */

namespace Instagram\API\Request;


use Instagram\Instagram;

class VideoUploadRequest extends AuthenticatedBaseRequest
{
    public function __construct(Instagram $instagram)
    {
        parent::__construct($instagram);
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
        // TODO: Implement getResponseObject() method.
    }
}