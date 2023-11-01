<?php

namespace App\Services\Vne;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Http\Client\Common\Exception\ClientErrorException;
use Http\Client\Exception;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiClient
{

    public $client;

    public $host;

    public $userName;

    public $passWord;

    public $accessToken;

    public $authResponse;

    public function __construct()
    {
        $this->client = new Client();
        $this->host = getenv("VNE_API_ENTRYPOINT");

    }


    /**
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Content-type' => 'application/json',
            'vne-Api-Key' => "7escgypvod07xob"
        ];
    }


    public function request($method, $url, $headers = [], $body = [])
    {

        try {

            $headers = array_merge($this->getHeaders(), $headers);

            if(strtoupper($method ) === "PATCH"){
                $headers['Content-type'] = 'application/merge-patch+json';
            }

            $response = $this->client->request($method,
                "{$this->host}{$url}",
                ['json' =>
                    $body,
                    'headers' => $headers
                ]);
            $jsonData = $response->getBody()->getContents();

            return [
                "status" => true,
                "code" => $response->getStatusCode(),
                "content" => json_decode($jsonData, true)
            ];

        } catch (RequestException $exception) {


            if ($exception->getCode() == 401) {
                $getUser = [];

                if (array_key_exists('refresh_token', $_COOKIE)) {
                    $getUser = $this->getAuth($_COOKIE['refresh_token']);
                }
                
                if (!empty($getUser["token"])) {
                    $this->accessToken = $getUser["token"];
                    $this->setCookie($getUser["refresh_token"]);
                } else {
                    return new RedirectResponse('/login');
                }

            } else {
                $errorMsg = json_decode($exception->getResponse()->getBody()->getContents(), true);
//                var_dump($exception->getMessage());
//                exit;


                if(is_array($errorMsg)){
                        $errorMsg = (array_key_exists("violations", $errorMsg)) ? $errorMsg["violations"] : $errorMsg;
                }

                return [
                    "status" => false,
                    "code" => $exception->getCode(),
                    "content" => $errorMsg
                ];
            }
        }

    }


    public function patch($url, $headers = [], $body)
    {

        try {

            $headers = array_merge([
                'Content-type' => 'application/merge-patch+json',
                'Authorization' => "Bearer {$this->accessToken}"
            ],
                $headers);
            $response = $this->client->patch(
                "{$this->host}{$url}",
                [
                    "json" => $body,
                    "headers" => $headers
                ]
            );

            $jsonData = $response->getBody()->getContents();
            return [
                "status" => true,
                "code" => $response->getStatusCode(),
                "content" => json_decode($jsonData, true)
            ];

        } catch (RequestException $exception) {

            if ($exception->getCode() == 401) {
                $getUser = [];

                if (array_key_exists('refresh_token', $_COOKIE)) {
                    $getUser = $this->getAuth($_COOKIE['refresh_token']);
                }

                if (!empty($getUser["token"])) {
                    $this->accessToken = $getUser["token"];
                    $this->setCookie($getUser["refresh_token"]);
                } else {
                    return new RedirectResponse('/login');
                }

            } else {
                $errorMsg = json_decode($exception->getResponse()->getBody()->getContents(), true);

                return [
                    "status" => false,
                    "code" => $exception->getCode(),
                    "content" => (array_key_exists("violations", $errorMsg)) ? $errorMsg["violations"] : $errorMsg
                ];
            }
        }
    }

    public function setCookie($refreshToken)
    {
        $cookie = new Cookie('refresh_token', $refreshToken, strtotime('now + 1 month'));

        $responce = new Response();
        $responce->headers->setCookie($cookie);
        $responce->sendHeaders();
        return $cookie;
    }

    public function getAuth($post)
    {

        $client = new \GuzzleHttp\Client(["base_uri" => $this->host]);
        $options = [
            'form_params' => [
                "refresh_token" => $post
            ]
        ];
        $response = $client->post("{$this->host}token/refresh", $options);

        if ($response->getStatusCode() == 200) {

            $this->authResponse = $response->getBody()->getContents();
            $this->updateAccessToken();
            $tokens = json_decode($response->getBody()->getContents(), true);
            return $tokens;
        }

        return $response->getStatusCode();
    }

    public function galleryUpload($url,  $localImagePath, $localImageName, $headers = [])
    {
        $headers = array_merge(['Authorization' => "Bearer {$this->accessToken}"], $headers);

        $options = [
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => fopen($localImagePath, 'r'),
                    'filename' => $localImageName
                ]
            ],
            'headers' => $headers
        ];
        $response = $this->client->request("POST",
            "{$this->host}{$url}",
           $options);
        $jsonData = $response->getBody()->getContents();
        return [
            "status" => true,
            "code" => $response->getStatusCode(),
            "content" => json_decode($jsonData, true)
        ];
    }


}