<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

require __DIR__ . '/vendor/autoload.php';

$app = new Application();

$app->register('select:option')
  ->setDefinition([])
  ->setDescription("Select an option")
  ->setCode(function(InputInterface $input, OutputInterface $output) {
      $select = new \Dmouse\Console\Helper\SelectHelper($output);
      $select->setOptions([
          "option 1",
          "option 2",
          "option 3"
      ]);
      $option = $select->runSelect();

  })
;

$app->run();
