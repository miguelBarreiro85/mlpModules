<?php
namespace Mageplaza\HelloWorld\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AttributeSet extends Command
{
    protected function configure()
    {
        $this->setName('Mlp:AttributeSet');
        $this->setDescription('Demo command line');

        parent::configure();
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Hello World");
    }
}