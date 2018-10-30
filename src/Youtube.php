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

    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    public function getKey()
    {
        return $this->key;
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

    protected function request()
    {
        $this->setParams('key', $this->key);

        $url = $this->url . '?' . http_build_query($this->params);

        try {
            $response = $this->client->get($url)->getBody();
        } catch (ClientException $e) {
            $response = $e->getResponse()->getBody()->getContents();
        }

        return json_decode($response);
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

    public function getChannelByName($username, array $part = ['id', 'snippet', 'contentDetails', 'statistics', 'brandingSettings'])
    {
        $this->setResource('channels');

        $this->setParams([
            'part' => implode(', ', $part),
            'forUsername' => $username,
        ]);

        return $this->request();
    }

    public function getChannelById($id, array $part = ['id', 'snippet', 'contentDetails', 'statistics', 'brandingSettings'])
    {
        $this->setResource('channels');

        $this->setParams([
            'part' => implode(', ', $part),
            'id' => $id,
        ]);

        return $this->request();
    }
}
