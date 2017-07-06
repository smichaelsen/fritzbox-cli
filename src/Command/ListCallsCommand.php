<?php
namespace Maschinenraum\FbApi\Command;

use LucidFrame\Console\ConsoleTable;
use Maschinenraum\FbApi\Service\CallListService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCallsCommand extends Command
{

    protected function configure()
    {
        $this->setName('list-calls');
        $this->addOption('filter-device', 'd', InputOption::VALUE_OPTIONAL, 'Filters calls to match this device');
        $this->addOption('filter-external-number', 'e', InputOption::VALUE_OPTIONAL, 'Filters calls to match this external number');
        $this->addOption('filter-local-number', 'l', InputOption::VALUE_OPTIONAL, 'Filters calls to match this local number');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $title = 'Call list';
        $filters = [];
        if ($input->getOption('filter-local-number')) {
            $filters[CallListService::FILTER_LOCAL_NUMBER] = $input->getOption('filter-local-number');
            $title .= ' for local number ' . $input->getOption('filter-local-number');
        }
        if ($input->getOption('filter-external-number')) {
            $filters[CallListService::FILTER_EXTERNAL_NUMBER] = $input->getOption('filter-external-number');
            $title .= ' with external number ' . $input->getOption('filter-external-number');
        }
        if ($input->getOption('filter-device')) {
            $filters[CallListService::FILTER_DEVICE] = $input->getOption('filter-device');
            $title .= ' with device ' . $input->getOption('filter-device');
        }
        $calls = CallListService::getCalls($filters);
        echo $title . ': ' . "\n";
        $table = new ConsoleTable();
        $table->addHeader('Date / Time')->addHeader('Device')->addHeader('Type')->addHeader('Number');
        foreach ($calls as $call) {
            $number = $call->getExternalNumber();
            if ($call->getName()) {
                $number .= ' - ' . $call->getName();
            }
            $table->addRow([
                $call->getDate()->format('d.m.y H:i'),
                $call->getDevice(),
                $call->getTypeString(),
                $number,
            ]);
        }
        $table->display();
    }
}
