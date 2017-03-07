<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CodeChallenge\Models;

/**
 * Description of Appointment
 *
 * @author astridsynnoveschonemann
 */
class Appointment {
    
    /**
     * @var string
     */
    private $title;
    
    /**
     * @var \DateTime
     */
    private $begins;
    
    /**
     * @var \DateTime
     */
    private $ends;
    
    /**
     * @param string $title
     * @param \DateTime $begins
     * @param \DateTime $ends
     */
    function __construct(string $title, \DateTime $begins, \DateTime $ends) {
        $this->title = $title;
        $this->begins = $begins;
        $this->ends = $ends;
    }
    
    function getTitle() {
        return $this->title;
    }
    
    public function getTimeSlot(): \CodeChallenge\Models\TimeSlot{
        return new \CodeChallenge\Models\TimeSlot(
            $this->getBegins(), 
            $this->getEnds()
        );
    }

    function getBegins(): \DateTime {
        return $this->begins;
    }

    function getEnds(): \DateTime {
        return $this->ends;
    }
}