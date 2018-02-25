<?php

declare(strict_types=1);

namespace App;

use GuzzleHttp\Client;

/**
 * Class Message
 *
 * @package App
 */
class App
{
    /**
     * @var Client
     */
    public $client;

    /**
     * @var
     */
    public $config;

    /**
     * @var
     */
    public $date;

    /**
     * Message constructor.
     */
    public function __construct()
    {
        $this->config = [
            'login'    => env('GITHUB_LOGIN'),
            'password' => env('GITHUB_PASSWORD'),
            'owner'    => env('GITHUB_OWNER'),
            'repo'     => env('GITHUB_REPO'),
        ];

        $this->client = new Client([
            'base_uri' => 'https://api.github.com/',
            'timeout'  => 10.0,
            'auth'     => [
                $this->config['login'], $this->config['password']
            ]
        ]);
    }

    /**
     * @return mixed
     */
    public function getNotifications(){

        $response = $this->client->request('GET',
            "https://api.github.com/repos/{$this->config['owner']}/{$this->config['repo']}/notifications");

        $this->date = date(\DateTime::ISO8601);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @return mixed
     */
    public function readNotifications(){
        $response = $this->client->request('PUT',
            "https://api.github.com/notifications",[
                'body' => json_encode([
                    'last_read_at' => $this->date,
                ]),
            ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @return mixed
     */
    public function allIssues(): array
    {
        $response = $this->client->request('GET',
            "https://api.github.com/repos/{$this->config['owner']}/{$this->config['repo']}/issues");

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @return mixed
     */
    public function getIssues(): array
    {
        $response = $this->client->request('GET',
            "https://api.github.com/repos/{$this->config['owner']}/{$this->config['repo']}/issues");

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param int $comment
     * @return mixed
     */
    public function getComment(int $comment)
    {
        $response = $this->client->request('get',
            "repos/{$this->config['owner']}/{$this->config['repo']}/issues/{$comment}/comments");
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param int    $comment
     * @param string $text
     * @return mixed
     */
    public function addComment(int $comment, string $text)
    {
        $response = $this->client->request('post',
            "repos/{$this->config['owner']}/{$this->config['repo']}/issues/{$comment}/comments", [
                'body' => json_encode([
                    'body' => $text,
                ]),
            ]);
        return json_decode($response->getBody()->getContents(), true);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function unSubscription(int $id){
        $response = $this->client->request('DELETE',
            "/notifications/threads/{$id}/subscription", [
            ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $method
     * @param        $url
     * @return mixed
     */
    public function toFollow($method = 'GET',$url){
        $response = $this->client->request($method, $url);
        return json_decode($response->getBody()->getContents(), true);
    }


    /**
     * @param array $actions
     */
    public function run(array $actions)
    {
        $notifications = self::getNotifications();
        $last_key = count($actions) -1;

        foreach ($notifications as $notification){

            if($notification['subject']['type'] !== "Issue"){
                continue;
            }

            $issue = $this->toFollow('GET',$notification['subject']['url']);
            $message = '';
            $comments = [];

            if ($issue['comments'] > 0) {
                $comments = self::getComment($issue['number']);
            }

            foreach ($actions as $key => $action) {

                $action = new $action();

                if ($action->check($issue, $comments)) {
                    $message .= $action->action($issue, $comments);
                }

                if ($last_key === $key) {

                    if (!$action->stop && strlen(trim($message)) > 5) {
                        print_r(self::addComment($issue['number'], $message));
                    }
                    die();
                }
            }


        }

        self::readNotifications();
    }

}