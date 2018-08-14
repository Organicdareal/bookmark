<?php

namespace App\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Embed\Embed;
use App\Entity\Photo;
use App\Entity\Video;
use Embed\Exceptions\EmbedException;

/**
 * Created by PhpStorm.
 * User: ggicquel
 * Date: 14/08/2018
 * Time: 09:45
 */
class OEmbedFetcher
{
    public function fetchUrl(string $url){
        $info = Embed::create($url);

        return $info;
    }

    public function newUrl(string $url){
        $info = Embed::create($url);
        $providers = $info->getProviders();
        $oembed = $providers['oembed'];
        $bag = $oembed->getBag();

        if ($info->providerName == "Vimeo"){
            $link = new Video(new \DateTime("now"), $info->authorName, $info->title, $url, $info->width, $info->height, $bag->get('duration'));
        }elseif ($info->providerName == "Flickr"){
            $link = new Photo(new \DateTime("now"), $info->authorName, $info->title, $url, $info->width, $info->height);
        }else{
            throw new EmbedException("You can only add links from vimeo or flickr");
        }
        return $link;
    }
}