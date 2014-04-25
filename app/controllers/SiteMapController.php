<?php

class SiteMapController extends BaseController
{
    function getSiteMap()
    {
        $prevTime = date('c', time() - (60 * 60 * 24 * 30));

        $jobPostings = JobPosting::where('created_time', '>', $prevTime)->get();

        $urlset = new SimpleXMLElement('<urlset/>');
        $urlset->addAttribute("xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9");

        foreach ($jobPostings as $jobPosting)
        {
            $url = $urlset->addChild("url");
            $url->addChild("loc", URL::route('home') . "#$jobPosting->id");
//          $url->addChild("lastmod", $jobPosting->updated_at);
        }

        $response = Response::make($urlset->asXML(), "200");
        $response->header('Content-Type', "text/xml");

        return $response;
    }
} 