<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MeetingRoomControllerTest extends WebTestCase
{
    private const TEST_USER_NICKNAME = 'Test';
    private const TEST_USER_EMAIL = 'test@test.pl';

    public function testCanRenderInitialPage()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testInitialPageShowsSignupForm()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertGreaterThan(
            0,
            $crawler->filter('form:contains("Nickname")')->count()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter('form:contains("Email")')->count()
        );
    }

    public function testCanSubmitSignupForm()
    {
        $client = static::createClient();
        $this->submitForm($client);

        $this->assertTrue(
            $client->getResponse()->isRedirect('/confirm')
        );

        $session = $client->getContainer()->get('session');
        $this->assertTrue(
            $session->has('nickname')
        );
        $this->assertEquals(
            self::TEST_USER_NICKNAME,
            $session->get('nickname')
        );
        $this->assertTrue(
            $session->has('email')
        );
        $this->assertEquals(
            self::TEST_USER_EMAIL,
            $session->get('email')
        );
    }

    public function testCanRenderConfirmationPage()
    {
        $client = static::createClient();
        $this->submitForm($client);
        $crawler = $client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Confirm")')->count()
        );

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("'.self::TEST_USER_EMAIL.'")')->count()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("'.self::TEST_USER_NICKNAME.'")')->count()
        );
    }

    private function submitForm($client)
    {

        $crawler = $client->request('GET', '/');

        $form = $crawler->selectButton('form[save]')->form();
        $form['form[nickname]'] = self::TEST_USER_NICKNAME;
        $form['form[email]'] = self::TEST_USER_EMAIL;

        $client->submit($form);
    }

}
