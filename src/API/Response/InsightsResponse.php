<?php
/**
 * Created by PhpStorm.
 * User: alexok
 * Date: 28.05.17
 * Time: 13:31
 */

namespace Instagram\API\Response;


class InsightsResponse extends BaseResponse
{
    public $instagram_user;

    public function getData()
    {
        return [
            'general' => $this->getGeneralData(),
        ];
    }

    public function getGeneralData()
    {
        $data = [];

        if (isset($this->instagram_user->instagram_insights->nodes[0]->attachments->nodes)) {
            $items = $this->instagram_user->instagram_insights->nodes[0]->attachments->nodes;

            foreach ($items as $item) {
                $matches = null;

                if (preg_match('~(?P<count>\d+) (?P<key>.+)~', $item->title, $matches)) {
                    $key = str_replace(' ', '_', $matches['key']);
                    $data[$key] = [
                        'count' => $matches['count'],
                        'title' => $item->title,
                        'subtitle' => $item->subtitle,
                    ];
                }
            }
        }


        return $data;
    }
}