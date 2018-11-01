<?php

namespace Memo\Youtube;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Youtube
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $url = 'https://www.googleapis.com/youtube/v3';

    /**
     * @var string
     */
    protected $key;

    /**
     * @var array
     */
    protected $params = [
        'part' => 'id, snippet'
    ];
    
    /**
     * @param  string  $key
     * @return void
     */
    public function __construct($key)
    {
        $this->client = new Client();
        $this->key = $key;
    }

    /**
     * @param  string  $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param  string  $type
     * @return void
     */
    protected function setResource($type)
    {
        $this->url .= '/' . $type;
    }

    /**
     * @return void
     */
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

    /**
     * @return object
     */
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

    /**
     * @param  string  $type
     * @return $this
     */
    public function resource($type)
    {
        $this->setResource($type);

        return $this;
    }

    /**
     * @param  string|array  $part
     * @return $this
     */
    public function select($part)
    {
        $this->setParams('part', is_array($part) ? implode(', ', $part) : $part);

        return $this;
    }

    /**
     * @return $this
     */
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

    /**
     * @return object
     */
    public function get()
    {
        return $this->request();
    }

    /**
     * @param  string  $q
     * @return object
     */
    public function search($q)
    {
        $this->setResource('search');

        $this->setParams('q', $q);

        return $this->request();
    }

    /**
     * @param  string  $username
     * @param  string|array  $part
     * @return object
     */
    public function getChannelByName($username, $part = ['id', 'snippet', 'statistics'])
    {
        $this->setResource('channels');

        $this->setParams([
            'part' => is_array($part) ? implode(', ', $part) : $part,
            'forUsername' => $username,
        ]);

        return $this->request();
    }

    /**
     * @param  string  $username
     * @param  string|array  $part
     * @return object
     */
    public function getChannelById($id, $part = ['id', 'snippet', 'statistics'])
    {
        $this->setResource('channels');

        $this->setParams([
            'part' => is_array($part) ? implode(', ', $part) : $part,
            'id' => $id,
        ]);

        return $this->request();
    }
}
