<?php

namespace App\Command;

use App\OlxPartnerApi\Service\Category\ImportCategoriesService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:olx-partner:import:categories',
    description: 'Import categories from OLX Partner API. Run with \'--no-debug\' option!',
)]
class OlxPartnerImportCategoriesCommand extends Command
{
    public function __construct(
        private readonly ImportCategoriesService $importCategoriesService
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try{
            ($this->importCategoriesService)();
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        $io->success('Categories imported successfully.');
        return Command::SUCCESS;
    }

}
