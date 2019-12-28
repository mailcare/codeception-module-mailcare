<?php
namespace Codeception\Module;

use Codeception\Module;
use DateInterval;
use DateTime;
use Exception;

class MailCare extends Module
{
    public $client;
    protected $url = 'https://mailix.xyz/api';
    protected $userpwd;
    protected $timeoutInSeconds = 30;
    
    public function _initialize()
    {
        if (!empty($this->config['url'])) {
            $this->url = $this->config['url'];
        }
        if (!empty($this->config['login']) && !empty($this->config['password'])) {
            $this->userpwd = $this->config['login'] . ':' . $this->config['password'];
        }
        if (!empty($this->config['timeoutInSeconds'])) {
            $this->setTimeoutInSeconds($this->config['timeoutInSeconds']);
        }
    }

    private function setTimeoutInSeconds(int $timeoutInSeconds): void
    {
        if ($timeoutInSeconds == -1) {
            $timeoutInSeconds = $this->getTimeoutInSeconds();
        }
        $this->timeoutInSeconds = $timeoutInSeconds;
    }

    private function getTimeoutInSeconds(): int
    {
        return $this->timeoutInSeconds;
    }

    private function sanitizeCriterias(array $criterias): array
    {
        $criterias = array_merge($criterias, ['limit' => 1, 'page' => 1]);

        if (!empty($criterias['since'])) {
            try {
                $duration = new DateInterval($criterias['since']);
                $now = new DateTime();
                $now->sub($duration);
                $criterias['since'] = $now->format(DateTime::ISO8601);
            } catch (Exception $e) {
            }
        }

        return $criterias;
    }

    private function getJson(string $path, $data = []): array
    {
        $curl = curl_init();

        if (empty($data)) {
            $curlUrl = $this->url . $path;
        } else {
            $curlUrl = $this->url . $path . '?' .  http_build_query($data);
        }

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $curlUrl,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_USERAGENT => 'codeception-module-mailcare',
            CURLOPT_USERPWD => $this->userpwd
        ]);
        

        $resp = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if ($info['http_code'] == 200) {
            return json_decode($resp, true);
        }

        return [];
    }

    private function getBody(string $path, $data = []): string
    {
        $curl = curl_init();

        if (empty($data)) {
            $curlUrl = $this->url . $path;
        } else {
            $curlUrl = $this->url . $path . '?' .  http_build_query($data);
        }

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $curlUrl,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_USERAGENT => 'codeception-module-mailcare',
            CURLOPT_HTTPHEADER => ['Accept: text/html,text/plain,message/rfc2822'],
            CURLOPT_USERPWD => $this->userpwd
        ]);
        

        $resp = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if ($info['http_code'] == 200) {
            return $resp;
        }

        return '';
    }

    private function getLastEmailId(array $criterias): string
    {
        $end = microtime(true) + $this->getTimeoutInSeconds();

        $criterias = $this->sanitizeCriterias($criterias);

        $lastId = '';
        do {
            $body = $this->getJson('/emails', $criterias);

            if (!empty($body['data'][0]['id'])) {
                $lastId = $body['data'][0]['id'];
                break;
            }

            usleep(250000);
        } while ($end > microtime(true));

        return $lastId;
    }

    private function getEmailCount(array $criterias): int
    {
        $end = microtime(true) + $this->getTimeoutInSeconds();

        $criterias = $this->sanitizeCriterias($criterias);

        $count = 0;
        do {
            $body = $this->getJson('/emails', $criterias);

            if (!empty($body['meta']['total'])) {
                $count = $body['meta']['total'];
                break;
            }

            usleep(250000);
        } while ($end > microtime(true));

        return $count;
    }

    private function getEmailBody(string $id): string
    {
        $end = microtime(true) + $this->getTimeoutInSeconds();

        $body = '';
        do {
            $body = $this->getBody('/emails/'.$id);

            if (!empty($body)) {
                var_dump($body);
                $body = $body;
                break;
            }

            usleep(250000);
        } while ($end > microtime(true));

        return $body;
    }

    public function seeEmailCount(int $expectedCount, array $criterias, int $timeoutInSeconds = -1): void
    {
        $this->setTimeoutInSeconds($timeoutInSeconds);
        $actualCount = $this->getEmailCount($criterias);
        $this->assertEquals($expectedCount, $actualCount, "Failed asserting that {$actualCount} emails matches expected {$expectedCount}.");
    }

    public function seeEmail(array $criterias, int $timeoutInSeconds = -1): string
    {
        $this->setTimeoutInSeconds($timeoutInSeconds);
        $lastId = $this->getLastEmailId($criterias);
        $this->assertNotEmpty($lastId, "Failed asserting that at least one email has been found.");
        return $lastId;
    }

    public function dontSeeEmail(array $criterias, int $timeoutInSeconds = -1): void
    {
        $this->setTimeoutInSeconds($timeoutInSeconds);
        $lastId = $this->getLastEmailId($criterias);
        $this->assertEmpty($lastId, "Failed asserting that no email has been found.");
    }

    public function grabLinksInLastEmail(array $criterias, int $timeoutInSeconds = -1): array
    {
        $lastId = $this->seeEmail($criterias, $timeoutInSeconds);

        $body = $this->getEmailBody($lastId);

        preg_match_all('#\bhttp?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $body, $out);

        return $out[0];
    }

    public function grabTextInLastEmail(string $regex, array $criterias, int $timeoutInSeconds = -1): array
    {
        $lastId = $this->seeEmail($criterias, $timeoutInSeconds);

        $body = $this->getEmailBody($lastId);

        preg_match_all($regex, $body, $out);

        return $out;
    }
}
