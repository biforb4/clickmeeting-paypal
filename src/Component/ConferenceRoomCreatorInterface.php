<?php

namespace App\Component;


interface ConferenceRoomCreatorInterface
{
    /**
     * Creates a conference room and returns url for it.
     * @param string $roomName
     * @return string
     */
    public function createRoom(string $roomName): string;

}