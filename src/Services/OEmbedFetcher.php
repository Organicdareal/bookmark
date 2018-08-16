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
 *
 * Fetch oEmbed parameters.
 * This service uses oscarotero/Embed bundle.
 */
class OEmbedFetcher
{
    /**
     * @param string $url
     * @return \Embed\Adapters\Adapter
     *
     * Returns oEmbed fetched parameters
     */
    public function fetchUrl(string $url){
        $info = Embed::create($url);
        return $info;
    }

    /**
     * @param string $url
     * @return Photo|Video
     * @throws EmbedException
     *
     * Fetch oEmbed parameters, check if provider is accepted, then returns a relevant entity filled with fetched parameters
     */
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