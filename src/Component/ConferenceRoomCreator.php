<?php

namespace App\Component;


use App\Helper\ClickMeetingRestClient;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ConferenceRoomCreator implements ConferenceRoomCreatorInterface
{
    private $clickMeetingRestClient;
    private $session;

    public function __construct(ClickMeetingRestClient $clickMeetingRestClient, SessionInterface $session)
    {
        $this->clickMeetingRestClient = $clickMeetingRestClient;
        $this->session = $session;
    }

    /**
     * Creates a conference room and returns url for it.
     * @param string $roomName
     * @return string
     */
    public function createRoom(string $roomName): string
    {
        $conferenceRoomParams = [
            'name' => $roomName,
            'room_type' => 'meeting',
            'permanent_room' => 0,
            'access_type' => 1,
            'duration' => 1
        ];

        $room = $this->clickMeetingRestClient->addConference($conferenceRoomParams);
        $roomId = $room->room->id;
        $roomUrl = $room->room->room_url;

        $params = array(
            'email' => $this->session->get('email'),
            'nickname' => $this->session->get('nickname'),
            'role' => 'listener',
        );
        $hash = $this->clickMeetingRestClient->conferenceAutologinHash($roomId, $params);
        $autologinHash = $hash->autologin_hash;

        return $roomUrl . '?l=' . $autologinHash;
    }
}