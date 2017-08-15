<?php
/**
 * Created by PhpStorm.
 * User: alexok
 * Date: 21.07.17
 * Time: 16:55
 */

namespace Instagram\Util;


use InstagramAPI\ImageAutoResizer;
use InstagramAPI\MediaAutoResizer;
use InstagramAPI\Utils;

class Helper
{
    public static function generateUploadId()
    {
        return number_format(round(microtime(true) * 1000), 0, '', '');
    }

    public static function createVideoPreview($videoFilename)
    {
        $ffmpeg = '/usr/local/bin/ffmpeg';

        if (empty($videoFilename) || !is_file($videoFilename)) {
            throw new \InvalidArgumentException(sprintf('The video file "%s" does not exist on disk.', $videoFilename));
        }

        // Generate a temp thumbnail filename and delete if file already exists.
        $tmpPath = sys_get_temp_dir();
        $tmpFilename = $tmpPath .'/'. md5($videoFilename).'.jpg';
        $tmpFilename = str_replace('//', '/', $tmpFilename);

        if (is_file($tmpFilename)) {
            @unlink($tmpFilename);
        }

        try {
            // Capture a video preview snapshot to that file via FFMPEG.
            $command = escapeshellarg($ffmpeg).' -i '.escapeshellarg($videoFilename).' -f mjpeg -ss 00:00:01 -vframes 1 '.escapeshellarg($tmpFilename).' 2>&1';
            @exec($command, $output, $statusCode);

            // Check for processing errors.
            if ($statusCode !== 0) {
                throw new \RuntimeException('FFmpeg failed to generate a video thumbnail.');
            }

            // Automatically crop&resize the thumbnail to Instagram's requirements.
            $resizer = new MediaAutoResizer($tmpFilename);
            $jpegContents = file_get_contents($resizer->getFile()); // Process&get.
            $resizer->deleteFile();
            
            return $jpegContents;
        } finally {
            @unlink($tmpFilename);
        }
    }

    public static function getVideoInfo($path)
    {
        Utils::$ffprobeBin = '/usr/local/bin/ffprobe';
        return Utils::getVideoFileDetails($path);
    }

    public static function hashCode($string)
    {
        $result = 0;
        for ($i = 0, $len = strlen($string); $i < $len; $i++) {
            $result = (-$result + ($result << 5) + ord($string[$i])) & 0xFFFFFFFF;
        }
        if (PHP_INT_SIZE > 4) {
            if ($result > 0x7FFFFFFF) {
                $result -= 0x100000000;
            } elseif ($result < -0x80000000) {
                $result += 0x100000000;
            }
        }

        return $result;
    }
}