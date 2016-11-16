<?php
namespace CodeChallenge\Services;

class TaskRunner {
    
    /**
     * A cronjob or similar should run this every minute.
     */
    public static function RunDueTasks(){
        $query = new Query();
        $query->addFilter(
            TaskFinder::getMultipleStatusFilter(
                \CodeChallenge\Models\Task::STATUS_TO_BE_EXECUTED, 
                \CodeChallenge\Models\Task::STATUS_FOR_RETRY 
            )
        );
        $now = new \DateTime();
        $in_a_minute = new \DateTime();
        $in_a_minute->add(new \DateInterval('P60S')); //This period is dependent on the interval between runs
        $query->addFilter(
            TaskFinder::getExecutionTimeBetweenFilter(
                $now,
                $in_a_minute
            )
        );
        
        $due_tasks = TaskFinder::find($query);
        foreach($due_tasks as $due_task){
            $now = new \DateTime();
            $execution_time = $due_task->getExecutionTime();
            $time_to_run = $now->diff($execution_time);
            if($time_to_run->format('s') < 2){//Again dependent on that no more than 60 is returned, otherwise this is unpredictable
                //just execute, we're within "a few seconds"
                $due_task->execute();
            }
            else{
                $worker = new Worker($due_task);//this should somehow thread out
                $worker->runIn($time_to_run);
            }
        }
    }
}