<?php
/**
 * @file Example vote count.
 *
 * @see ./README.txt
 */

// Display errors (just for testing).
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

// Load the DrooPHP library.
require '../../../library.php';

$file = 'data/SanFrancisco-DA-2011.blt';

$options = array(
  'filename' => $file,
  'allow_skipped' => TRUE,
  'allow_repeat' => TRUE,
  'allow_equal' => TRUE,
  'cache_dir' => '../cache',
);

$count = new DrooPHP\Count();
$count->getSource()->setOptions($options);
print $count->run();
