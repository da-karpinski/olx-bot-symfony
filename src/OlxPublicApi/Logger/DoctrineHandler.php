<?php

namespace App\OlxPublicApi\Logger;

use App\Entity\OlxPublicLog;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

class DoctrineHandler extends AbstractProcessingHandler
{
    private bool $initialized = false;
    private $entityManager;
    private $channel = 'OlxPublicApi';

    public function __construct(EntityManagerInterface $entityManager, int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        $this->entityManager = $entityManager;
        parent::__construct($level, $bubble);
    }

    protected function write(LogRecord $record): void
    {

        if (!$this->initialized) {
            $this->initialize();
        }

        if ($this->channel != $record->channel) {
            return;
        }

        $log = new OlxPublicLog();
        $log->setMessage($record->formatted);
        $log->setCreatedAt($record->datetime);
        $log->setLevel($record->level->getName());

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    private function initialize()
    {
        $this->initialized = true;
    }

}