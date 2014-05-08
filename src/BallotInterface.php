<?php
/**
 * @file
 * Interface for a ballot.
 */

namespace DrooPHP;

interface BallotInterface {

  /**
   * Constructor.
   *
   * @param array $ranking
   *    The ranking, expressed as an array of candidate IDs keyed by their
   *    preference level (e.g. the second preference candidate is keyed by 2).
   *    Each array element could itself be an array of candidate IDs, if equal
   *    ranking is allowed.
   *
   * @param int|float $value
   *    The value of this ballot (default: 1).
   */
  public function __construct(array $ranking, $value = 1);

  /**
   * Get the ranking.
   *
   * @return array
   */
  public function getRanking();

  /**
   * Get the candidate(s) ranked at the given preference level.
   *
   * @param int $level
   *
   * @return array
   */
  public function getPreference($level);

  /**
   * Get the ballot value.
   *
   * @return int|float
   */
  public function getValue();

  /**
   * Add to the ballot's value.
   *
   * @param int $amount
   */
  public function addValue($amount);

}
