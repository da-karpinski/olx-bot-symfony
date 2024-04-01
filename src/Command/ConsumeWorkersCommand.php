<?php

namespace App\Command;

use App\Entity\Worker;
use App\Service\OfferService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:consume:workers',
    description: 'This command consumes workers from the queue and fetches offers from OLX.',
)]
class ConsumeWorkersCommand extends Command
{

    const DEFAULT_LIMIT = 50;
    private int $offset = 0;
    private int $quantity = 0;
    private array $workers = [];
    private int $consumed = 0;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly OfferService $offerService,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('quantity', null, InputOption::VALUE_OPTIONAL, 'Quantity of workers to consume', 10)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->quantity = intval($input->getOption('quantity'));

        $io->info('Start consuming ' . $this->quantity . ' workers...');

        $this->getWorkers();

        if(empty($this->workers)){
            $io->success('No workers to consume.');
            return Command::SUCCESS;
        }

        foreach($this->workers as $worker) {
            $io->info('Consuming worker with ID: ' . $worker->getId());
            $results = $this->offerService->processOffersForWorker($worker);
            $io->info(sprintf('Worker with ID: %s. New offers: %s. Updated offers: %s.',
                $worker->getId(),
                $results['new'],
                $results['updated']
            ));
        }

        $io->success(
            sprintf('Consumed %s workers. Peak memory usage: %s bytes. Execution time: %s seconds.',
                $this->consumed,
                memory_get_peak_usage(),
                round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],2)
            )
        );

        return Command::SUCCESS;
    }

    private function getWorkers(): void
    {
        $workers = $this->em->getRepository(Worker::class)->findWorkersToConsume(self::DEFAULT_LIMIT, $this->offset);

        if(empty($workers)){
            return;
        }

        foreach ($workers as $worker) {

            $diff = (new \DateTimeImmutable())->format('U') - $worker->getLastExecutedAt()?->format('U');
            $executionIntervalSec = $worker->getExecutionInterval() * 60;

            if ($diff >= $executionIntervalSec) {
                $this->workers[] = $worker;
                $this->consumed++;
            }

            if($this->consumed === $this->quantity){
                return;
            }
        }

        if($this->consumed < $this->quantity){
            $this->offset += self::DEFAULT_LIMIT;
            $this->getWorkers();
        }
    }

}
