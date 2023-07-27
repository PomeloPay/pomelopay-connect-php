<?php

namespace PomeloPayConnect\Options;

class Expires
{
    /**
     * @var \DateTime
     */
    private $now;

    public function __construct(\DateTime $now)
    {
        $this->now = $now->setTimezone(new \DateTimeZone('UTC'));
    }

    /**
     * @return string
     */
    public function getIsoString()
    {
        return $this->now->format('Y-m-d\TH:i:s.u\Z');
    }

    /**
     * @param int $validForHours
     * @return string
     */
    public function getExpiryDateAsIsoString($validForHours)
    {
        $this->now->modify('+' . (int) $validForHours . ' hours');
        return $this->now->format('Y-m-d\TH:i:s.u\Z');
    }
}
