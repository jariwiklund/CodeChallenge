<?php
namespace CodeChallenge\Services;

class TaskRunner {
    
    /**
     * A cronjob or similar should run this every minute.
     * 
     * As the list of tasks to run will grow, it might be prudent to divide it somehow,
     * so that we are confident all tasks will run at the given time
     */
    public static function RunDueTasks(){
        $query = new Query();
        
        //this filtering cold/should perhaps be one filter in TaskFinder, but for this challenge lets just be verbose
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
            $due_task->setStatusToInPipeLine();
            $now = new \DateTime();
            $execution_time = $due_task->getExecutionTime();
            $time_to_run = $now->diff($execution_time);
            $worker = new Worker($due_task);//The worker should thread out somehow, so that we can be sure the whole list of tasks can be traversed before the execution_time is reached
            if($time_to_run->format('s') < 2){//Again dependent on that no more than 60 is returned, otherwise this will be unpredictable
                $worker->runNow();
            }
            else{
                $worker->runIn($time_to_run);
            }
        }
    }
}