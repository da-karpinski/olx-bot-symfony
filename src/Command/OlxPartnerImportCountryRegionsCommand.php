<?php

namespace App\Command;

use App\OlxPartnerApi\Service\Location\ImportCountryRegionsService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:olx-partner:import:country-regions',
    description: 'Import country regions from OLX Partner API.',
)]
class OlxPartnerImportCountryRegionsCommand extends Command
{
    public function __construct(
        private readonly ImportCountryRegionsService $importCountryRegionsService
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try{
            ($this->importCountryRegionsService)();
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        $io->success('Country regions imported successfully.');
        return Command::SUCCESS;
    }

}
