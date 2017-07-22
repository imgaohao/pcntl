<?php

interface Processable
{
    public function run();
}

class ProcessTask implements Processable
{

    public function run()
    {
        echo 123 . PHP_EOL;sleep(rand(1, 5));
    }
}

class Process implements Processable
{
    public function __construct()
    {
        
    }

    public function run()
    {

    }
}

class ProcessPool
{
    private $_processNum;

    private $_processTask = array();

    private $_pid = array();

    public function __construct($processNum)
    {
        $this->setProcessNum($processNum);
    }

    /**
     * @return mixed
     */
    public function getProcessNum()
    {
        return $this->_processNum;
    }

    /**
     * @param mixed $_jobProcessNum
     */
    public function setProcessNum($processNum)
    {
        $this->_processNum = $processNum;
    }

    /**
     * @param Processable $processableTask
     * @return mixed
     */
    public function addProcessTask(Processable $processableTask)
    {
        $this->_processTask[] = $processableTask;
        return array_pop(array_keys($this->_processTask));
    }

    public function removeProcessTask($index)
    {
        unset($this->_processTask[$index]);
    }

    public function addPid($pid)
    {
        $this->_pid[$pid] = $pid;
    }

    public function getPid()
    {
        return $this->_pid;
    }

    public function removePid($pid)
    {
        unset($this->_pid[$pid]);
    }

    public function submit(Processable $processableTask)
    {
        $index = $this->addProcessTask($processableTask);

        $jobProcessNum = $this->getProcessNum();

        $pid = pcntl_fork();
        if ($pid == -1) {
            die("cannot fork");
        } else if ($pid) {
            $this->addPid($pid);
            $this->setProcessNum(--$jobProcessNum);
            if ($jobProcessNum == 0) {
                $pid = pcntl_wait($status);
                $this->removePid($pid);
                $this->setProcessNum(++$jobProcessNum);
            }
        } else {
            $processableTask->run();
            $this->removeProcessTask($index);
            exit();
        }
    }

    public function shutdown()
    {
        while ($this->getPid()) {
            $pid = pcntl_wait($status);
            $this->removePid($pid);
        }
    }
}

$processPool = new ProcessPool(3);
$processPool->submit(new ProcessTask());
$processPool->submit(new ProcessTask());
$processPool->submit(new ProcessTask());
$processPool->submit(new ProcessTask());
$processPool->submit(new ProcessTask());
$processPool->submit(new ProcessTask());
$processPool->submit(new ProcessTask());
$processPool->submit(new ProcessTask());
$processPool->submit(new ProcessTask());
$processPool->submit(new ProcessTask());

echo 'aaa' . PHP_EOL;

$processPool->shutdown();