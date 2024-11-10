<?php

namespace App\Weeek;

use Monolog\Logger;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;
use Webmozart\Assert\Assert;

class ApiClient
{
    protected HttpClientInterface $client;
    protected Logger $l;

    public function __construct(Logger $logger)
    {
        Assert::stringNotEmpty($_ENV['WEEEK_WS_TOKEN'] ?? '', "Не указан токен WEEEK");
        $httpOpts = (new HttpOptions())->verifyHost(false)->verifyPeer(false)->setHeaders(['Authorization' => "Bearer {$_ENV['WEEEK_WS_TOKEN']}"])->toArray();
        $this->client = HttpClient::create($httpOpts);
        $this->l = $logger->withName('WeeekApi');
    }

    static int $getTasksReqCount = 0;

    /**
     * @throws Throwable
     */
    public function getTasks($offset = 0, $perPage = 25): array
    {
        $this::$getTasksReqCount++;
        $curReqNum = $this::$getTasksReqCount;

        $startTime = microtime(true);

        $response = $this->client->request('GET', "https://api.weeek.net/public/v1/tm/tasks?perPage=$perPage&offset=$offset&all=1");
        $responseArray = $response->toArray();

        $startTimeApiQSCurl = $response->getInfo('start_time');

        $timeApiQS = number_format((microtime(true) - $startTime), 4, ',', ' ');
        $timeApiQSCurl = number_format((microtime(true) - $startTimeApiQSCurl), 4, ',', ' ');
        $this->l->info("getTasks Api call #$curReqNum: {$timeApiQS}s (CURL: {$timeApiQSCurl}s)");

        $tasks = $responseArray['tasks'] ?? [];

        if ($responseArray['hasMore'] === true) {
            $tasks = array_merge($tasks, $this->getTasks(($offset + $perPage)));
        }

        if ($curReqNum === 1) {
            $timeApiQAllS = number_format((microtime(true) - $startTime), 4, ',', ' ');
            $this->l->info("getTasks SUMMARY: time={$timeApiQAllS}s count=" . count($tasks));
        }

        return $tasks;
    }
}