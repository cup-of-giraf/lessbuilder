<?php

namespace Cog\Less\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class BuildCommand extends Command
{
    const LESS_EXTENSION = 'less';
    const CSS_EXTENSION = 'css';
    const DEFAULT_WATCHER_INTERVAL = 2;

    protected $less;

    protected $finder;

    protected $output;

    protected $less_files;


    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Build less files into css')
            ->addArgument('source', InputArgument::REQUIRED, 'File or directory to build')
            ->addOption('target', 't', InputOption::VALUE_OPTIONAL, 'Output file or directory')
            ->addOption('watch', 'w', InputOption::VALUE_NONE, 'Start watcher')
            ->addOption('watch-interval', 'i', InputOption::VALUE_OPTIONAL, 'Set watcher interval in seconds', self::DEFAULT_WATCHER_INTERVAL);
        $this->less = new \lessc();
        $this->less_files = array();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $source = new \SplFileObject($input->getArgument('source'));
        $target = $input->getOption('target') != null ? new \SplFileInfo($input->getOption('target')) : null;
        $watch = $input->getOption('watch');
        $watch_interval = $input->getOption('watch-interval');

        do {
            $this->build($source, $target);
            if( $watch ) {
                sleep( $watch_interval );
            }
        } while( $watch );
    }

    /**
     * @param $source
     * @param $target
     * @return bool|\InvalidArgumentException
     */
    protected function build($source, $target) {
        if( $source->isDir() ) {

            return $this->compileDirectory( $source, $target );
        }
        clearstatcache();

        return $this->compile( new \SplFileObject($source), $target);
    }

    /**
     * @param \SplFileInfo $source
     * @param \SplFileInfo $target
     * @return bool
     */
    protected function compile( \SplFileInfo $source, \SplFileInfo $target=null ) {

        if( $this->isChanged($source) == false ) {

            return false;
        }
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

        return true;
    }

    /**
     * @param \SplFileInfo $file
     * @return bool
     */
    protected function isChanged(\SplFileInfo $file) {
        $key = $file->getPathname();
        $value = null;
        if( isset($this->less_files[$key])) {
            $value = $this->less_files[$key];
        }
        $this->less_files[$key] = $file->getMTime();

        return $value != $file->getMTime();
    }

    /**
     * @param \SplFileInfo|\SplFileObject $source
     * @param \SplFileInfo|\SplFileObject $target
     * @return \SplFileInfo|\SplFileObject
     */
    protected function getRealTarget( \SplFileInfo $source, \SplFileInfo $target=null ) {

        if( $target == null ) {
            $dir = $source->getPath();
        } elseif( $target->isDir() == false ) {

            return $target;
        } else {
            $dir = $target->getPathname();
        }

        return new \SplFileInfo( sprintf(
            '%s/%s%s',
            $dir,
            $source->getBasename( $source->getExtension() ),
            self::CSS_EXTENSION
        ));
    }

    /**
     * @param \SplFileObject $source
     * @param \SplFileInfo $target
     * @return \InvalidArgumentException
     */
    protected function compileDirectory(\SplFileObject $source, \SplFileInfo $target=null) {
        $return = false;
        foreach( $this->getFinder($source) as $file ) {
            if( $this->compile( $file, $target ) ) {
                $return = true;
            }
        }

        return $return;
    }

    /**
     * @param \SplFileObject $source
     * @return \Symfony\Component\Finder\Finder
     */
    protected function getFinder( \SplFileObject $source ) {
        if( $this->finder == null ) {
            $this->finder = new Finder();
            $this->finder
                ->in( $source->getPathname() )
                ->name(sprintf('*.%s',self::LESS_EXTENSION))
                ->files();
        }

        return $this->finder;
    }
}

