<?php

namespace App\Integration\Telegram\Command;

use App\Integration\Telegram\HttpClient;
use App\Integration\Telegram\Model\TelegramApi;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

#[AsCommand(
    name: 'app:integration:telegram:set-webhook',
    description: 'This command allows you to set a webhook URL for the Telegram integration.',
)]
class IntegrationTelegramSetWebhookCommand extends Command
{
    public function __construct(
        #[Autowire(env: 'TELEGRAM_BOT_TOKEN')]
        private readonly string $botToken,
        #[Autowire(env: 'TELEGRAM_BOT_API_URL')]
        private readonly string $apiUrl,
        #[Autowire(env: 'TELEGRAM_BOT_WEBHOOK_SECRET_TOKEN')]
        private readonly string $secretToken,
        private readonly HttpClient $apiClient,
        private readonly RouterInterface $router,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Setting up a webhook for the Telegram integration...');

        $model = TelegramApi::setWebHook;

        $webhookUrl = $this->router->generate('integration_telegram_webhook', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $io->info('Generated webhook URL: ' . $webhookUrl);

        $response = $this->apiClient->request(
            $model->method(),
            $this->apiUrl . str_replace('{token}', $this->botToken, $model->uri()),
            [
                'headers' => $model->headers(),
                'json' => [
                    'url' => $webhookUrl,
                    'secret_token' => $this->secretToken,
                    'allowed_updates' => ['message', 'my_chat_member'],
                ],
            ]
        );

        if($response[$model->dataKey()] !== true){
            $io->error('Failed to set the webhook for the Telegram integration. Response from API: ' . json_encode($response));
            return Command::FAILURE;
        }

        $io->success('Webhook for the Telegram integration has been successfully set!');
        return Command::SUCCESS;
    }

}
