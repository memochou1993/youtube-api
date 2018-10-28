<?php

namespace Memo\Youtube;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Youtube
{
    protected $client;
    protected $url;
    protected $key;
    protected $params;
    
    public function __construct($key)
    {
        $this->client = new Client();

        $this->url = 'https://www.googleapis.com/youtube/v3';

        $this->key = $key;
    }

    public function getChannel($username, $part = ['id', 'snippet', 'statistics'])
    {
        $this->url .= '/channels';

        $this->params = [
            'forUsername' => $username,
            'part' => implode(', ', $part),
        ];

        return $this->getResponse($url);
    }

    public function getChannelVideos($channelId, $maxResults = 10, $part = ['id', 'snippet'])
    {
        $this->url .= '/search';

        $this->params = [
            'type' => 'video',
            'channelId' => $channelId,
            'maxResults' => $maxResults,
            'part' => implode(', ', $part),
        ];

        return $this->getResponse();
    }

    protected function getResponse()
    {
        $this->url .= '?key=' . $this->key . '&' . http_build_query($this->params);

        try {
            $response = $this->client->get($this->url)->getBody();

            echo json_encode([
                'success' => true,
                'response' => json_decode($response),
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse()->getBody()->getContents();

            echo json_encode([
                'success' => false,
                'response' => json_decode($response)->error,
            ]);
        }
    }
}
