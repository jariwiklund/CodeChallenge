<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CodeChallenge\Models;

/**
 * Description of TimeSlot
 *
 * @author astridsynnoveschonemann
 */
class TimeSlot {
    
    /**
     * @var \DateTime
     */
    private $begin;
    
    /**
     * @var \DateTime
     */
    private $end;
    
    public function __construct(\DateTime $begin, \DateTime $end) {
        $this->begin = $begin;
        $this->end = $end;
    }
    
    public function getBegin(): \DateTime {
        return $this->begin;
    }

    public function getEnd(): \DateTime {
        return $this->end;
    }
}