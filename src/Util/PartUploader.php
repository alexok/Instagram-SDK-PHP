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

class PartUploader
{
    const MIN_CHUNK_SIZE = 204800;
    const MAX_CHUNK_SIZE = 5242880;

    private $path;
    private $urls;

    public function __construct($path, $urls)
    {
        $this->path = $path;
        $this->urls = $urls;
    }

    public function upload($uploadId)
    {
        $length = filesize($this->path);
        $sessionId = sprintf('%s-%d', $uploadId, Helper::hashCode($this->path));
        $uploadUrl = array_shift($this->urls);

        $offset = 0;
        $chunk = min($length, self::MIN_CHUNK_SIZE);
//        $attempt = 0;

        $handle = fopen($this->path, 'rb');

        try {
            while (true) {
//                if ($uploadUrl === null) {
//                    $uploadUrl = array_shift($uploadUrls);
//
//                    $attempt = 1; // As if "++$attempt" had ran once, above.
//                    $offset = 0;
//                    $chunk = min($length, self::MIN_CHUNK_SIZE);
//                }

                $chunkContent = fread($handle, $chunk);
                $contentRange = sprintf('bytes %d-%d/%d', $offset, $offset + $chunk - 1, $length);

                $curl = new Curl();
                $curl->setHeader('Content-Type', 'application/octet-stream');
                $curl->setHeader('Session-ID', $sessionId);
                $curl->setHeader('Content-Disposition', 'attachment; filename="video.mov"');
                $curl->setHeader('Content-Range', $contentRange);
                $curl->setHeader('job', $uploadUrl->job);

                $start = microtime(true);
                $response = $curl->post($uploadUrl->url, $chunkContent);
                $end = microtime(true);

                $httpCode = $curl->httpStatusCode;
                $rangeHeader = $curl->responseHeaders['Range'];

                $newChunkSize = (int) ($chunk / ($end - $start) * 5);
                $newChunkSize = min(self::MAX_CHUNK_SIZE, max(self::MIN_CHUNK_SIZE, $newChunkSize));

                switch ($curl->httpStatusCode) {
                    case 200:
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

    /*
     * $request = new VideoUploadRequest($this, $path, $uploadParams);
        $uploadResponse = $request->execute();
        $uploadRawResponse = $request->getCachedResponse();

        if (!$uploadResponse->isOk()) {
            throw new InstagramException(sprintf('Failed upload video: [%s] $s', $response->getStatus(), $response->getMessage()));
        }
    */
}