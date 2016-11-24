<?php
use PHPUnit\Framework\TestCase;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TestTimeslotCalculator
 *
 * @author astridsynnoveschonemann
 */
class TimeTableComputerTest extends TestCase {
    
    public function testCalculateNumTimeslotsPerDayForResolution()
    {
        $res = \CodeChallenge\Services\TimeTableComputer::CalculateNumTimeslotsPerDayForResolution(1);
        $this->assertEquals(1440, $res);
        
        $res = \CodeChallenge\Services\TimeTableComputer::CalculateNumTimeslotsPerDayForResolution(5);
        $this->assertEquals(288, $res);
        
        $res = \CodeChallenge\Services\TimeTableComputer::CalculateNumTimeslotsPerDayForResolution(15);
        $this->assertEquals(96, $res);
        
        $res = \CodeChallenge\Services\TimeTableComputer::CalculateNumTimeslotsPerDayForResolution(60);
        $this->assertEquals(24, $res);
    }
    
    public function testCalculateMinuteOfTheDay(){
        $date_time = new \DateTime('2016-11-24 21:13:00');
        $res = \CodeChallenge\Services\TimeTableComputer::CalculateMinuteOfTheDay($date_time);
        $this->assertEquals(1273, $res);
    }
    
    public function testMapTimeIntervalToBinRepresentation(){
        $begin = new \DateTime('2016-11-24 00:15:00');
        $end = new \DateTime('2016-11-24 14:15:00');
        $res1 = \CodeChallenge\Services\TimeTableComputer::MapTimeIntervalToBinRepresentation($begin, $end, 60);
        $this->assertEquals('111111111111111000000000', $res1);
        
        $begin = new \DateTime('2016-11-24 12:15:00');
        $end = new \DateTime('2016-11-24 14:15:00');
        $res = \CodeChallenge\Services\TimeTableComputer::MapTimeIntervalToBinRepresentation($begin, $end, 60);
        $this->assertEquals('000000000000111000000000', $res);
        
        $begin = new \DateTime('2016-11-24 17:00:00');
        $end = new \DateTime('2016-11-24 18:00:00');
        $res = \CodeChallenge\Services\TimeTableComputer::MapTimeIntervalToBinRepresentation($begin, $end, 60);
        $this->assertEquals('000000000000000001000000', $res);
        
        $begin = new \DateTime('2016-11-24 23:00:00');
        $end = new \DateTime('2016-11-24 23:59:00');
        $res2 = \CodeChallenge\Services\TimeTableComputer::MapTimeIntervalToBinRepresentation($begin, $end, 60);
        $this->assertEquals('000000000000000000000001', $res2);
        
        $hex1 = '0x'.dechex(bindec($res1));
        $gmp1 = gmp_init($hex1);
        
        $hex2 = '0x'.dechex(bindec($res2));
        $gmp2 = gmp_init($hex2);
        
        $gmp_and = gmp_or($gmp1, $gmp2);
        
        $this->assertEquals('111111111111111000000001', gmp_strval($gmp_and, 2));
    }
}