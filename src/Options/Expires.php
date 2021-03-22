<?php

namespace PomeloPayConnect\Options;


class Expires
{
    private $now;

    public function __construct(\DateTime $now)
    {
        $this->now = $now->setTimezone(new \DateTimeZone('UTC'));
    }

    public function getIsoString()
    {
        return $this->now->format('Y-m-d\TH:i:s.u\Z');
    }

    public function getExpiryDateAsIsoString($validForHours)
    {
        $this->now->modify('+' . (int) $validForHours . ' hours');
        return $this->now->format('Y-m-d\TH:i:s.u\Z');
    }
}