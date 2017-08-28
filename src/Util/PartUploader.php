<?php
/**
 * Created by PhpStorm.
 * User: alexok
 * Date: 03.08.17
 * Time: 15:05
 */

namespace Instagram\Util;


use Curl\Curl;
use Instagram\API\Framework\InstagramException;
use Instagram\API\Request\VideoUploadRequest;
use Instagram\API\Response\VideoUploadResponse;

class PartUploader
{
    const MIN_CHUNK_SIZE = 204800;
    const MAX_CHUNK_SIZE = 5242880;

    private $path;
    private $urls;
    private $uploadId;

    public function __construct($path, $uploadId, $urls)
    {
        $this->path = $path;
        $this->uploadId = $uploadId;
        $this->urls = $urls;
    }

    /**
     * @throws InstagramException
     * @return VideoUploadResponse
     */
    public function upload($instagram)
    {
        $length = filesize($this->path);
        $sessionId = sprintf('%s-%d', $this->uploadId, Helper::hashCode($this->path));
        $uploadUrl = null;

        $offset = 0;
        $chunk = min($length, self::MIN_CHUNK_SIZE);
        $attempt = 0;

        $handle = fopen($this->path, 'rb');

        try {
            while (true) {
                if (++$attempt > 3) {
                    throw new \InstagramAPI\Exception\UploadFailedException(sprintf('Upload of "%s" failed. All retries have failed.', basename($this->path)));
                }

                if ($uploadUrl === null) {
                    $uploadUrl = array_shift($this->urls);
//
                    $attempt = 1; // As if "++$attempt" had ran once, above.
                    $offset = 0;
//                    $chunk = min($length, self::MIN_CHUNK_SIZE);
                }

                if ($offset + $chunk > $length) {
                    $chunk = $length - $offset;
                }

                $chunkContent = fread($handle, $chunk);
                $contentRange = sprintf('bytes %d-%d/%d', $offset, $offset + $chunk - 1, $length);

                $curl = new Curl();
                $curl->setHeader('Content-Type', 'application/octet-stream');
                $curl->setHeader('Session-ID', $sessionId);
                $curl->setHeader('Content-Disposition', 'attachment; filename="video.mov"');
                $curl->setHeader('Content-Range', $contentRange);
                $curl->setHeader('job', $uploadUrl->job);

                $start = microtime(true);
                $response = $curl->post($this->formatUrl($uploadUrl->url), $chunkContent);
                $end = microtime(true);

                $rangeHeader = $curl->responseHeaders['Range'];

                $newChunkSize = (int) ($chunk / ($end - $start) * 5);
                $newChunkSize = min(self::MAX_CHUNK_SIZE, max(self::MIN_CHUNK_SIZE, $newChunkSize));

                switch ($curl->httpStatusCode) {
                    case 200:
                        return $this->createResponse($response);
                        break;

                    case 201:
                        if (!$rangeHeader) {
                            $uploadUrl = null;
                            break;
                        }

                        // TODO parse missing ranges
                        $range = $this->parseRange($rangeHeader);

                        if ($range) {
                            $offset = $range[0];
                            $chunk = min($newChunkSize, $range[1] - $range[0] + 1);
                        } else {
                            $chunk = min($newChunkSize, $length - $offset);
                        }

                        $attempt = 0;
                        break;

                    case 400:
                    case 403:
                    case 511:
                    case 502:
                        throw new InstagramException(sprintf("Upload of \"%s\" failed. Instagram's server returned HTTP status \"%d\".",
                            $this->path, $curl->httpStatusCode
                        ));
                    case 422:
                        throw new InstagramException(sprintf("Upload of \"%s\" failed. Instagram's server says that the video is corrupt.",
                            $this->path, $curl->httpStatusCode
                        ));
                }


            }
        } finally {
            fclose($handle);
        }

    }

    /**
     * @param $instagram
     * @return VideoUploadResponse
     * @throws InstagramException
     */
    public function uploadPerRequest($instagram)
    {
        $uploadUrl = array_shift($this->urls);

        $request = new VideoUploadRequest($instagram, $this->path, $this->uploadId, $uploadUrl);
        $response = $request->execute();

        if (!$response->isOk()) {
            throw new InstagramException(sprintf('Failed upload video: [%s] $s', $response->getStatus(), $response->getMessage()));
        }

        return $response;
    }

    private function parseRange($rangeLine)
    {
        preg_match('/(?<start>\d+)-(?<end>\d+)\/(?<total>\d+)/', $rangeLine, $matches);

        if (!count($matches)) {
            return false;
        }

        $range = [
            $matches['start'],
            $matches['end'],
        ];

        $length = $matches['total'];

        if ($range[0] == 0) {
            $result = [$range[1] + 1, $length];
        } else {
            $result = [0, $range[0] - 1];
        }

        return $result;
    }

    private function createResponse($response)
    {
        $mapper = new CustomJsonMapper();
        $response = $mapper->map($response, new VideoUploadResponse());

        if (!$response->isOk()) {
            throw new InstagramException(sprintf('Failed upload video: [%s] $s', $response->getStatus(), $response->getMessage()));
        }

        return $response;
    }

    private function formatUrl($url)
    {
        if (preg_match('/upload(?P<sub>.+)\.instagram\.com/', $url, $matches)) {
            return str_replace($matches['sub'], '', $url);
        }

        return $url;
    }
}
