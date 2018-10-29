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
        $this->params = [];
    }

    protected function setResource($type)
    {
        $this->url .= '/' . $type;
    }

    protected function setParams()
    {
        if (func_num_args() === 1) {
            foreach (func_get_arg(0) as $key => $value) {
                $this->params[$key] = $value;
            }
        }

        if (func_num_args() === 2) {
            $this->params[func_get_arg(0)] = func_get_arg(1);
        }
    }

    public function resource($type)
    {
        $this->setResource($type);

        return $this;
    }

    public function select($part = ['id', 'snippet'])
    {
        $this->setParams('part', is_array($part) ? implode(', ', $part) : $part);

        return $this;
    }

    public function where()
    {
        if (func_num_args() === 1) {
            $this->setParams(func_get_arg(0));
        }

        if (func_num_args() === 2) {
            $this->setParams(func_get_arg(0), func_get_arg(1));
        }

        return $this;
    }

    public function get()
    {
        return $this->request();
    }

    public function search($q)
    {
        $this->setResource('search');

        $this->setParams('q', $q);

        return $this->request();
    }

    public function getChannel($username)
    {
        $this->setResource('channels');

        $this->setParams([
            'part' => 'id, snippet, statistics',
            'forUsername' => $username,
        ]);

        return $this->request();
    }

    protected function request()
    {
        $this->setParams('key', $this->key);

        $url = $this->url . '?' . http_build_query($this->params);

        try {
            $response = $this->client->get($url)->getBody();

            echo json_encode([
                'success' => true,
                'request' => [
                    'url' => $this->url,
                    'params' => $this->params,
                ],
                'response' => json_decode($response),
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse()->getBody()->getContents();

            echo json_encode([
                'success' => false,
                'request' => [
                    'url' => $this->url,
                    'params' => $this->params,
                ],
                'response' => json_decode($response),
            ]);
        }
    }
}
