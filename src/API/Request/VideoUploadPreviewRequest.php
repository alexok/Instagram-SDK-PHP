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
    private $file;

    public function __construct(Instagram $instagram, $uploadId, $fileContent)
    {
        parent::__construct($instagram);

        $this->file = $this->createFile($fileContent);

        $this->addParam('_uuid', $instagram->getUUID());
        $this->addParam('_csrftoken', $instagram->getCSRFToken());
        $this->addParam('upload_id', $uploadId);
        $this->addParam('image_compression', '{"lib_name":"jt","lib_version":"1.3.0","quality":"87"}');
        $this->addFile('photo', new RequestFile($this->file, "application/octet-stream", sprintf("pending_media_%s.jpg", Helper::generateUploadId())));
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

    public function execute()
    {
        $status = parent::execute();

        if (is_file($this->file))
            @unlink($this->file);

        return $status;
    }

    private function createFile($content)
    {
        $path = sys_get_temp_dir(). DIRECTORY_SEPARATOR. uniqid().'.jpg';
        file_put_contents($path, $content);
        return $path;
    }
}