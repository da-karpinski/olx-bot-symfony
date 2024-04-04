<?php

namespace App\Logger;

use App\Entity\IntegrationLog;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

class IntegrationDoctrineHandler extends AbstractProcessingHandler
{
    private bool $initialized = false;
    private $entityManager;
    private $channel = 'Integration';

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

        $log = new IntegrationLog();
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