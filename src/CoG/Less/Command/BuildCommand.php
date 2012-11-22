<?php

namespace Cog\Less\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command
{
    protected $less;

    protected $output;

    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Build less files into css')
            ->addArgument('source', InputArgument::REQUIRED, 'File or directory to build')
            ->addOption('target', 't', InputOption::VALUE_OPTIONAL, 'Output file or directory');
        $this->less = new \lessc();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $source = new \SplFileObject($input->getArgument('source'));
        $target = $input->getOption('target') != null ? new \SplFileInfo($input->getOption('target')) : null;

        if( $source->isDir() ) {
            throw new \InvalidArgumentException('Directories are not yet supported');
        }

        $this->compile( new \SplFileObject($source), $target);

    }

    protected function compile( \SplFileObject $source, \SplFileInfo $target=null ) {

        $real_target = $this->getRealTarget($source, $target);
        $this->output->write(
            sprintf(
                'Building <info>%s</info> into <info>%s</info> ',
                $source->getPathname(),
                $real_target->getPathname()
            )
        );
        $this->less->compileFile( $source->getPathname(), $real_target->getPathname() );
        $this->output->writeln(sprintf('<%s>[%s]</%s>', 'info', ' OK ', 'info' ));
    }

    /**
    * @param \SplFileObject $source
    * @param \SplFileObject $target
    * @return \SplFileInfo|\SplFileObject
     */
    protected function getRealTarget( \SplFileObject $source, \SplFileInfo $target=null ) {

        if( $target == null ) {
            $dir = $source->getPath();
        } elseif( $target->isDir() == false ) {
            return $target;
        } else {
            $dir = $target->getPathname();
        }

        return new \SplFileInfo( sprintf('%s/%scss', $dir,  $source->getBasename( $source->getExtension() )));
    }
}

