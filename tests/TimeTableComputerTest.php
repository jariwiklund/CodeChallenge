<?php
use PHPUnit\Framework\TestCase;

/**
 * Description of TestTimeslotCalculator
 *
 * @author Jari Wiklund
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
        
        //todo: test exceptions
        
    }
    
    public function testMapDayScheduleToBinRepresentation(){
        $schedule = new \CodeChallenge\Models\Schedule(
            array(
                new \CodeChallenge\Models\Appointment(
                    'TestAppointment1', 
                    new \DateTime('2016-11-24 00:15:00'), 
                    new \DateTime('2016-11-24 14:15:00')
                ),
                new \CodeChallenge\Models\Appointment(
                    'TestAppointment2', 
                    new \DateTime('2016-11-24 23:00:00'), 
                    new \DateTime('2016-11-24 23:59:00')
                )
                ,
                new \CodeChallenge\Models\Appointment(
                    'TestAppointment2', 
                    new \DateTime('2016-11-24 19:00:00'), 
                    new \DateTime('2016-11-24 20:00:00')
                )
            ),
            new \CodeChallenge\Models\Person('TestPerson')
        );
        
        $result = \CodeChallenge\Services\TimeTableComputer::MapDayScheduleToBinRepresentation(
            $schedule, 
            60
        );
        $this->assertEquals('111111111111111000010001', $result);
    }
    
    public function testMapSchedulesToAvailabilityInBinaryString(){
        $schedules = array(
            new \CodeChallenge\Models\DaySchedule(
                array(
                    new \CodeChallenge\Models\Appointment(
                        'TestAppointment1', 
                        new \DateTime('2016-11-24 00:15:00'), 
                        new \DateTime('2016-11-24 14:15:00')
                    ),
                    new \CodeChallenge\Models\Appointment(
                        'TestAppointment2', 
                        new \DateTime('2016-11-24 23:00:00'), 
                        new \DateTime('2016-11-24 23:59:00')
                    ),
                    new \CodeChallenge\Models\Appointment(
                        'TestAppointment2', 
                        new \DateTime('2016-11-24 19:00:00'), 
                        new \DateTime('2016-11-24 20:00:00')
                    )
                ),
                new \CodeChallenge\Models\Person('TestPerson')
            ),
            new \CodeChallenge\Models\DaySchedule(
                array(
                    new \CodeChallenge\Models\Appointment(
                        'TestAppointment1', 
                        new \DateTime('2016-11-24 14:15:00'), 
                        new \DateTime('2016-11-24 17:15:00')
                    ),
                    new \CodeChallenge\Models\Appointment(
                        'TestAppointment2', 
                        new \DateTime('2016-11-24 19:00:00'), 
                        new \DateTime('2016-11-24 20:00:00')
                    )
                ),
                new \CodeChallenge\Models\Person('TestPerson2')
            )
        );
        $result = \CodeChallenge\Services\TimeTableComputer::MapSchedulesToAvailabilityInBinaryString(
            $schedules, 
            60
        );
        $this->assertEquals('111111111111111111010001', $result);
    }
    
    function testFindAvailTimeslotsInSchedule(){
        
        $schedule = new \CodeChallenge\Models\DaySchedule(
            array(
                new \CodeChallenge\Models\Appointment(
                    'TestAppointment1', 
                    new \DateTime('2016-11-24 00:00:00'), 
                    new \DateTime('2016-11-24 14:15:00')
                ),
                new \CodeChallenge\Models\Appointment(
                    'TestAppointment2', 
                    new \DateTime('2016-11-24 23:00:00'), 
                    new \DateTime('2016-11-24 23:59:00')
                ),
                new \CodeChallenge\Models\Appointment(
                    'TestAppointment2', 
                    new \DateTime('2016-11-24 19:00:00'), 
                    new \DateTime('2016-11-24 20:00:00')
                )
            ),
            new \CodeChallenge\Models\Person('TestPerson')
        );
        
        $res = \CodeChallenge\Services\TimeTableComputer::FindAvailTimeslotsInSchedule(
            $schedule, 
            15, 
            1
        );
        $this->assertEquals(31, count($res));
    }
    
    function testMapBinStringToFreeSchedule(){
        $bin_string = '111111111111111111010001';
        $date_offset = new \DateTime('1979-05-29');
        $free_schedule = \CodeChallenge\Services\TimeTableComputer::MapBinStringToFreeSchedule(
            $bin_string, 
            $date_offset
        );
        $this->assertEquals(2, count($free_schedule->getAppointments()));
        $first_appointment = $free_schedule->getAppointments()[0];
        $this->assertEquals('1979-05-29 18:00:00', $first_appointment->getBegins()->format('Y-m-d H:i:s'), print_r($first_appointment, true));
        $second_appointment = $free_schedule->getAppointments()[1];
        $this->assertEquals('1979-05-29 20:00:00', $second_appointment->getBegins()->format('Y-m-d H:i:s'), print_r($second_appointment, true));
        $this->assertEquals('1979-05-29 23:00:00', $second_appointment->getEnds()->format('Y-m-d H:i:s'), print_r($second_appointment, true));
    }
}