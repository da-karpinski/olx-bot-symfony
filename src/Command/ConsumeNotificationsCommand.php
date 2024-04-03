<?php

namespace App\Command;

use App\Entity\Notification;
use App\Integration\IntegrationInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:consume:notifications',
    description: 'This command consumes notifications and send them to users.',
)]
class ConsumeNotificationsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly IntegrationInterface $integration
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('quantity', null, InputOption::VALUE_OPTIONAL, 'Quantity of notifications to consume', 50)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $quantity = intval($input->getOption('quantity'));

        $io->info('Start consuming ' . $quantity . ' notifications...');

        $notifications = $this->em->getRepository(Notification::class)->findNotificationsToConsume($quantity);

        if(empty($notifications)){
            $io->success('No notifications to consume.');
            return Command::SUCCESS;
        }

        $results = [
            'success' => 0,
            'failed' => 0,
        ];

        foreach($notifications as $notification) {
            $io->info(sprintf('Sending notification with ID %s via %s integration...',
                $notification->getId(),
                $notification->getIntegration()->getIntegrationType()->getName()
            ));

            try{
                $this->integration->sendNotification($notification);
                $notification->setSentAt(new \DateTimeImmutable());
                $this->em->persist($notification);
                $this->em->flush();
                $io->info('Notification with ID ' . $notification->getId() . ' sent successfully.');
                $results['success']++;
            }catch (\Exception $e){
                $io->error('Error sending notification with ID ' . $notification->getId() . '. Error: ' . $e->getMessage());
                $results['failed']++;
            }
        }

        $io->success(
            sprintf('Notifications has been sent. Successfully: %s; Failed: %s. Peak memory usage: %s bytes. Execution time: %s seconds.',
                $results['success'],
                $results['failed'],
                memory_get_peak_usage(),
                round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],2)
            )
        );

        return Command::SUCCESS;
    }

}
