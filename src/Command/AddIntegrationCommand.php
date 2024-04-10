<?php

namespace App\Command;

use App\Entity\IntegrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:integration:add',
    description: 'This command adds an integration type to the system.',
)]
class AddIntegrationCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Add new integration wizard');

        $integrationTypeCode = $io->ask('Integration Code');

        if($this->em->getRepository(IntegrationType::class)->findOneBy(['integrationCode' => $integrationTypeCode])){
            $io->error('Integration with this code already exists.');
            return Command::FAILURE;
        }

        $integrationTypeName = $io->ask('Integration Name');
        $enabled = $io->choice('Enable this integration after creation?', ['yes', 'no'], 'yes') === 'yes';
        $locales = $io->ask('Enter supported locales (comma separated)', 'en,pl');

        $locales = explode(',', $locales);

        $integrationType = new IntegrationType();
        $integrationType->setIntegrationCode($integrationTypeCode)
            ->setName($integrationTypeName)
            ->setEnabled($enabled)
            ->setLocales($locales);

        $this->em->persist($integrationType);
        $this->em->flush();

        $io->success(sprintf('Integration "%s" added successfully.', $integrationTypeName));

        return Command::SUCCESS;
    }

}
