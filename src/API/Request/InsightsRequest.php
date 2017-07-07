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
    public function __construct(Instagram $instagram, $day)
    {
        parent::__construct($instagram);

        $this->addParam('show_promotions_in_landing_page', 'true');
        $this->addParam('first', $day);
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
