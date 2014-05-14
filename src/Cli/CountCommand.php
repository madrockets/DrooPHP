<?php
/**
 * @file
 * Command-line interface.
 */

namespace DrooPHP\Cli;

use DrooPHP\Count;
use DrooPHP\Exception\UsageException;
use DrooPHP\Formatter\Text;
use DrooPHP\Method\Stv;
use DrooPHP\Source\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CountCommand extends Command {

  /**
   * @{inheritdoc}
   */
  protected function configure() {
    $this->setName('count')
      ->setDescription('Count the votes in an election')
      ->addArgument('filename',
        InputArgument::REQUIRED,
        'The name of the ballot file'
      )
      ->addOption('method', 'm', InputOption::VALUE_REQUIRED,
        'The counting method (default: STV).'
      )
      ->addOption('format', 'f', InputOption::VALUE_REQUIRED,
        'The output format (default: text).'
      )
      ->addOption('allow-invalid', 'i', InputOption::VALUE_NONE,
        'Enable to allow invalid ballots.'
      )
      ->addOption('allow-equal', NULL, InputOption::VALUE_NONE,
        'Enable to allow equal rankings.'
      )
      ->addOption('allow-repeat', NULL, InputOption::VALUE_NONE,
        'Enable to allow repeat rankings.'
      )
      ->addOption('allow-skipped', NULL, InputOption::VALUE_NONE,
        'Enable to allow skipped rankings.'
      );
  }

  /**
   * @{inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $filename = $input->getArgument('filename');

    // Validate the given file name.
    if (!file_exists($filename)) {
      throw new UsageException('The file does not exist: ' . $filename);
    }
    if (is_dir($filename)) {
      throw new UsageException('The file is a directory: ' . $filename);
    }
    if (!is_readable($filename)) {
      throw new UsageException('Cannot read file: ' . $filename);
    }
    if (function_exists('finfo_open')) {
      $finfo = finfo_open(FILEINFO_MIME);
      $mime_type = array_shift(explode(';', finfo_file($finfo, $filename)));
      if ($mime_type != 'text/plain') {
        throw new UsageException('Invalid file type: ' . $mime_type);
      }
    }

    // Set up a cache directory for the command-line user.
    $cache_enable = TRUE;
    $cache_dir = rtrim(sys_get_temp_dir(), '\/') . DIRECTORY_SEPARATOR . 'droophp';
    if (!file_exists($cache_dir) && !mkdir($cache_dir, 0744, TRUE)) {
      $cache_dir = FALSE;
      $cache_enable = FALSE;
    }
    if ($cache_dir && !is_writable($cache_dir) || !is_dir($cache_dir)) {
      $cache_dir = FALSE;
      $cache_enable = FALSE;
    }

    // Set up the election source.
    $source = new File([
      'filename' => $filename,
      'cache_enable' => $cache_enable,
      'cache_dir' => $cache_dir,
      'allow_invalid' => $input->getOption('allow-invalid'),
    ]);

    if (!$input->getOption('allow-invalid')) {
      $source->setOption('allow_equal', $input->getOption('allow-equal'));
      $source->setOption('allow_repeat', $input->getOption('allow-repeat'));
      $source->setOption('allow_skipped', $input->getOption('allow-skipped'));
    }

    // Set up the count.
    $count = new Count([
      'formatter' => $input->getOption('format') ? : new Text(),
      'source' => $source,
      'method' => $input->getOption('method') ? : new Stv(),
    ]);

    // Run the count, and output the result.
    $output->writeln($count->getOutput());
  }

}