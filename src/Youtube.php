<?php

namespace Memo\Youtube;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Youtube
{
    protected $client;
    protected $url;
    protected $key;
    protected $part;
    protected $params;
    protected $q;
    
    public function __construct($key)
    {
        $this->client = new Client();
        $this->url = 'https://www.googleapis.com/youtube/v3';
        $this->key = $key;
        $this->part = ['id', 'snippet'];
        $this->params = [];
        $this->q = '';
    }

    public function resource($type)
    {
        $this->url .= '/' . $type;

        return $this;
    }

    public function select($part)
    {
        $this->part = is_array($part) ? $part : explode(', ', $part); 

        return $this;
    }

    public function where()
    {
        if (func_num_args() == 1 && is_array(func_get_arg(0))) {
            $this->params = func_get_arg(0);
        }

        if (func_num_args() == 2 && is_string(func_get_arg(0)) && is_string(func_get_arg(1))) {
            $this->params[func_get_arg(0)] = func_get_arg(1);
        }

        return $this;
    }

    public function get()
    {
        return $this->request();
    }

    public function search($q)
    {
        $this->url .= '/search';

        $this->q = $q;

        return $this->request();
    }

    public function getChannel($username, $part = ['id', 'snippet', 'statistics'])
    {
        $this->url .= '/channels';

        $this->part = is_array($part) ? $part : implode(', ', $part); 

        $this->params = [
            'forUsername' => $username,
        ];

        return $this->request();
    }

    protected function request()
    {
        $this->url .= 
            '?' . 'key=' . $this->key . 
            '&' . 'part=' . implode(', ', $this->part) . 
            '&' . http_build_query($this->params);

        try {
            $response = $this->client->get($this->url)->getBody();

            echo json_encode([
                'url' => $this->url,
                'success' => true,
                'response' => json_decode($response),
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse()->getBody()->getContents();

            echo json_encode([
                'url' => $this->url,
                'success' => false,
                'response' => json_decode($response),
            ]);
        }
    }
}
