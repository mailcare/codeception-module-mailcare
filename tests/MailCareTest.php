<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Codeception\Module\MailCare;
use Codeception\Util\Stub;

final class MailCareTest extends TestCase
{
    protected $module;

    protected function setUp(): void
    {
        $this->module = Stub::make('\Codeception\Module\MailCare');
        $this->module->setUrl('http://localhost:8000');
    }

    public function testSeeEmailCount(): void
    {
        $this->module->seeEmailCount(1, [
            'inbox' => 'jane@example.org',
            'sender' => 'no-reply@company.com',
            'subject' => 'Welcome John!',
            'since' => 'PT2M',
        ], 30);
    }

    public function testSeeEmail(): void
    {
        $this->module->seeEmail([
            'inbox' => 'jane@example.org',
            'sender' => 'no-reply@company.com',
            'subject' => 'Welcome John!',
            'since' => 'PT2M',
        ], 30);
    }

    public function testDontSeeEmail(): void
    {
        $this->module->dontSeeEmail([
            'inbox' => 'john@example.org',
            'since' => 'PT2M',
        ], 1);
    }

    public function testGrabLinksInLastEmail(): void
    {
        $links = $this->module->grabLinksInLastEmail([
            'inbox' => 'jane@example.org',
            'since' => 'PT2M',
        ], 1);
        $this->assertEquals(['https://mailcare.io'], $links);
    }

    public function testGrabTextInLastEmail(): void
    {
        $matchesText = $this->module->grabTextInLastEmail('#Password: (?<password>\S+)#', [
            'inbox' => 'jane@example.org',
            'subject' => 'Your credentials',
            'since' => 'PT2M',
        ], 30);
        
        $this->assertEquals([
            0 => ['Password: 24iBfvmMEB7isr'],
            'password' => ['24iBfvmMEB7isr'],
            1 => ['24iBfvmMEB7isr'],
        ], $matchesText);
    }
}
