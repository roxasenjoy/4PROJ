<?php

namespace App\Service;

use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;

class GlobalService
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

    }

    public function getTodayDate(): \DateTime
    {
        $date = new \DateTime();
        $date->setTimezone(new DateTimeZone('Europe/Paris'));
        $date->getTimestamp();

        return $date;
    }


}