<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 28/04/2018
 * Time: 15:54
 */

namespace App\Component;


use App\Helper\ClickMeetingRestClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class ConferenceRoomCreatorTest extends TestCase
{
    public function testCanCreateRoom()
    {
        $session = new Session(new MockArraySessionStorage());
        $session->set('email', 'test@tester.pl');
        $session->set('nickname', 'nickname');

        $restClient = new ClickMeetingRestClient(['api_key' => getenv('CLICKMEETING_API_KEY')]);

        $conferenceRoomCreator = new ConferenceRoomCreator(
            $restClient,
            $session
        );

        $url = $conferenceRoomCreator->createRoom('test');

        $this->assertRegExp('/https:\/\/([a-zA-Z0-9]+)\.clickmeeting.com\/test\?l/', $url);

        $activeRooms = $restClient->conferences('active');
        foreach($activeRooms as $room) {
            if($room->name === 'test') {
                $restClient->deleteConference($room->id);
            }
        }
    }

}
