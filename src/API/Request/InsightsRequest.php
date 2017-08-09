<?php
/**
 * Created by PhpStorm.
 * User: alexok
 * Date: 28.05.17
 * Time: 13:25
 */

namespace Instagram\API\Request;


use Instagram\API\Response\InsightsResponse;
use Instagram\Instagram;

class InsightsRequest extends AuthenticatedBaseRequest
{
    const PAGE_POSTS = 1;
    const PAGE_HISTORY = 2;
    const PAGE_FOLLOWERS = 3;
    const PAGE_PROMO = 4;

    public function __construct(Instagram $instagram, $day, $extraPage = false)
    {
        parent::__construct($instagram);

        $this->addParam('show_promotions_in_landing_page', 'true');
        $this->addParam('first', $day);

        switch ($extraPage) {
            case self::PAGE_POSTS:
                $this->addParam('page_type', 'SEE_ALL_TOP_MEDIA');
                $this->addParam('timeframe', 'THREE_MONTHS');
                break;

            case self::PAGE_HISTORY:
                // TODO
                break;

            case self::PAGE_FOLLOWERS:
                $this->addParam('page_type', 'AUDIENCE');
                $this->addParam('timeframe', 'ONE_WEEK');
                break;

            case self::PAGE_PROMO:
                // TODO
                break;
        }
    }

    public function getMethod()
    {
        return self::GET;
    }

    public function getEndpoint()
    {
        return '/v1/insights/account_organic_insights/';
    }

    public function getResponseObject()
    {
        return new InsightsResponse();
    }

    /**
     * @return InsightsResponse
     */
    public function execute()
    {
        return parent::execute();
    }
}
