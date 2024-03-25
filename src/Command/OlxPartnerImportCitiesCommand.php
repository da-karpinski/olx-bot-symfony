<?php

namespace App\Command;

use App\OlxPartnerApi\Service\Location\ImportCitiesService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsCommand(
    name: 'app:olx-partner:import:cities',
    description: 'Import cities from OLX Partner API. Run with \'--no-debug\' option!',
)]
class OlxPartnerImportCitiesCommand extends Command
{
    public function __construct(
        private readonly ImportCitiesService $importCitiesService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'limit', 500)
            ->addOption('offset', null, InputOption::VALUE_OPTIONAL, 'offset', 0)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $limit = intval($input->getOption('limit'));
        $offset = intval($input->getOption('offset'));

        while(true) {
            try{
                ($this->importCitiesService)($limit, $offset);
            } catch (\Exception $e) {

                if($e instanceof NotFoundHttpException){
                    $io->info("No more cities to import. Range: " . $offset . "-" . $offset+$limit);
                    break;
                }else{
                    $io->error($e->getMessage());
                    return Command::FAILURE;
                }
            }
            $offset += $limit;
            $io->info("Imported cities from range " . $offset . "-" . $offset+$limit);
        }

        $io->success('Cities imported successfully.');
        return Command::SUCCESS;
    }

}
