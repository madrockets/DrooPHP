<?php
/**
 * @file
 * Class representing a candidate in an election.
 */

namespace DrooPHP;

use DrooPHP\Exception\CandidateException;

class Candidate implements CandidateInterface {

  public $name;

  protected $id;
  protected $log = [];
  protected $state;
  protected $surplus = 0;
  protected $votes = 0;

  protected static $id_increment = 1;

  /**
   * @{inheritdoc}
   */
  public function __construct($name, $id = NULL) {
    $this->name = $name;
    $this->id = $id !== NULL ? $id : self::$id_increment++;
    $this->state = self::STATE_HOPEFUL;
  }

  /**
   * @{inheritdoc}
   */
  public function getVotes() {
    return $this->votes;
  }

  /**
   * @{inheritdoc}
   */
  public function addVotes($votes) {
    $this->votes += $votes;
  }

  /**
   * @{inheritdoc}
   */
  public function transferVotes($amount, CandidateInterface $to, $precision = 5) {
    if (round($this->votes, $precision) < round($amount, $precision)) {
      throw new CandidateException('Not enough votes to transfer');
    }
    $this->votes -= $amount;
    $to->addVotes($amount);
    $display_precision = $precision >= 2 ? 2 : $precision;
    $this->log(sprintf('Transferred %s votes to %s', number_format($amount, $display_precision), $to->getName()));
    $to->log(sprintf('Received %s votes from %s', number_format($amount, $display_precision), $this->name));
  }

  /**
   * @{inheritdoc}
   */
  public function log($message) {
    $this->log[] = $message;
  }

  /**
   * @{inheritdoc}
   */
  public function getLog($reset = FALSE) {
    $log = $this->log;
    if ($reset) {
      $this->log = [];
    }
    return $log;
  }

  /**
   * @{inheritdoc}
   */
  public function getSurplus() {
    return $this->surplus;
  }

  /**
   * @{inheritdoc}
   */
  public function setSurplus($amount, $increment = FALSE) {
    $this->surplus = $increment ? $this->surplus + $amount : $amount;
  }

  /**
   * @{inheritdoc}
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @{inheritdoc}
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @{inheritdoc}
   */
  public function getState($formatted = FALSE) {
    return $formatted ? $this->getFormattedState() : $this->state;
  }

  /**
   * @{inheritdoc}
   */
  public function setState($state) {
    $this->state = $state;
  }

  /**
   * @{inheritdoc}
   */
  protected function getFormattedState() {
    switch ($this->state) {
      case self::STATE_DEFEATED:
        return 'Defeated';
      case self::STATE_WITHDRAWN:
        return 'Withdrawn';
      case self::STATE_ELECTED:
        return 'Elected';
      case self::STATE_HOPEFUL:
        return 'Hopeful';
    }
    return 'Unknown';
  }

}
