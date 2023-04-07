<?php

namespace App\Command;

use App\Service\Index\IndexExportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'index:export:xml',
    description: 'Export index data to a xml file',
)]
class IndexExportCommand extends Command
{
    public function __construct(
        private readonly IndexExportService $indexExportService,
    ) {
        parent::__construct();
    }
    protected function configure(): void
    {
        
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $this->indexExportService->exportToXml();
        
        return Command::SUCCESS;
    }
}
