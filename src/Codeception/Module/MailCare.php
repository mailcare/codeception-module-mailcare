<?php
namespace Codeception\Module;

use Codeception\Module;

class MailCare extends Module
{

    public $client;
    protected $baseUrl = 'https://mailix.xyz/api';



    public function greet($name)
    {
        $this->debug("Hello {$name}!");
    }

    private function getEmails(array $criterias, int $timeoutInSecond): int
    {
        $end = microtime(true) + $timeoutInSecond;

        $criterias = array_merge($criterias, ['limit' => 1, 'page' => 1]);

        $nbEmails = 0;
        do
        {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $this->baseUrl . '/emails?' .  http_build_query($criterias),
                CURLOPT_CUSTOMREQUEST => 'GET', 
                CURLOPT_USERAGENT => 'codeception-module-mailcare'
            ]);

            $resp = curl_exec($curl);
            $info = curl_getinfo($curl);

            $body = json_decode($resp, true);

            if ($info['http_code'] == 200 && $body['meta']['total'] >=1) {
                $nbEmails = $body['meta']['total'];
                break;
            }

            usleep(250000);
        } while ($end > microtime(true));

        curl_close($curl);
        return $nbEmails;
    }

    public function seeEmail(array $criterias, int $timeoutInSecond = 30): void
    {
        $nbEmails = $this->getEmails($criterias, $timeoutInSecond);
        $this->assertGreaterThanOrEqual(1, $nbEmails, "Failed asserting that at least one email has been found.");
    }

    public function dontSeeEmail(array $criterias, int $timeoutInSecond = 30): void
    {
        $nbEmails = $this->getEmails($criterias, $timeoutInSecond);
        $this->assertEquals(0, $nbEmails, "Failed asserting that {$nbEmails} email(s) matches expected 0 email.");
    }
}