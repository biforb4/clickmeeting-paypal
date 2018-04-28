<?php

namespace App\Component;


interface ConferenceRoomCreatorInterface
{
    /**
     * Creates a conference room and returns url for it.
     */
    public function createRoom(): string;

}