<?php
namespace Maschinenraum\FbApi\Command;

use LucidFrame\Console\ConsoleTable;
use Maschinenraum\FbApi\Model\Call;
use Maschinenraum\FbApi\Service\CallListService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
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
        $this->addOption('missed', 'm', InputOption::VALUE_NONE, 'Show only missed calls');
        $this->addOption('since', 's', InputOption::VALUE_OPTIONAL, 'Since UNIX timestamp');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $title = 'Call list';
        $filters = [];
        if ($input->getOption('missed')) {
            $filters[CallListService::FILTER_TYPE] = Call::TYPE_INCOMING_MISSED;
            $title = 'Missed call list';
        }
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
        if ($input->getOption('since')) {
            $filters[CallListService::FILTER_SINCE] = $input->getOption('since');
            $title .= ' since ' . date('d.m.Y - H:i', $input->getOption('since'));
        }
        $calls = CallListService::getCalls($filters);
        echo $title . ': ' . "\n\n";
        $table = new ConsoleTable();
        $table->addHeader('Date / Time')->addHeader('Device')->addHeader('Type')->addHeader('Number');
        $output->getFormatter()->setStyle('incoming', new OutputFormatterStyle('green'));
        $output->getFormatter()->setStyle('missed', new OutputFormatterStyle('red'));
        $output->getFormatter()->setStyle('outgoing', new OutputFormatterStyle('blue'));
        foreach ($calls as $call) {
            $number = $call->getExternalNumber();
            if ($call->getName()) {
                $number .= ' - ' . $call->getName();
            }
            switch ($call->getType()) {
                case Call::TYPE_INCOMING:
                    $type = '<incoming>' . $call->getTypeString() . '</incoming>';
                    break;
                case Call::TYPE_INCOMING_MISSED:
                    $type = '<missed>' . $call->getTypeString() . '</missed>';
                    break;
                case Call::TYPE_OUTGOING;
                    $type = '<outgoing>' . $call->getTypeString() . '</outgoing>';
                    break;
                default:
                    $type = $call->getTypeString();
            }
            $table->addRow([
                $call->getDate()->format('d.m.y H:i'),
                $call->getDevice(),
                $output->getFormatter()->format($type),
                $number,
            ]);
        }
        echo $table->hideBorder()->getTable() . "\n";
    }
}
