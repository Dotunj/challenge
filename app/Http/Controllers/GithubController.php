<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class GithubController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.github.com',
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);
    }

    public function index()
    {
        $this->fetchScoreFromUsername('dotunj');
    }

    public function fetchScoreFromUsername($username)
    {
        $events = $this->fetchUserEventsFromGithub($username);

        $allEvent = new Collection($events);

        $collection = $allEvent->map(function($event) {
            dd($event['type']);
           $this->determineScoreFromType($event['type']);
        });

        $totalScore = $collection->all();

        dd($totalScore);
    }

    protected function fetchUserEventsFromGithub($username)
    {
        $response = $this->client->request('GET', '/users' . '/' . $username . '/events');

        $body = $response->getBody();

        $events = json_decode($body, true);

        return $events;
    }

    protected function determineScoreFromType($type)
    {
        switch($type) {
            case 'PushEvent':
            $score = 10;
            break;

            case 'PullRequestEvent';
            $score = 5;
            break;

            case 'IssueCommentEvent';
            $score = 4;
            break;

            default: 
            $score = 1;
            break;
        }

        return $score;
    }
}
