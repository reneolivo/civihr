<?php

use Civi\Test\HeadlessInterface;
use Civi\Test\TransactionalInterface;
use CRM_HRLeaveAndAbsences_BAO_BroughtForward as BroughtForward;

/**
 * Class api_v3_BroughtForwardTest
 *
 * @group headless
 */
class api_v3_BroughtForwardTest extends PHPUnit_Framework_TestCase implements
  HeadlessInterface,
  TransactionalInterface {

  public function setUpHeadless() {
    return \Civi\Test::headless()->installMe(__DIR__)->apply();
  }

  public function setUp() {
    // In order to make tests simpler, we disable the foreign key checks,
    // as a way to allow the creation of brought forward records related
    // to a non-existing entitlement
    CRM_Core_DAO::executeQuery("SET foreign_key_checks = 0;");
  }

  public function tearDown() {
    CRM_Core_DAO::executeQuery("SET foreign_key_checks = 1;");
  }

  public function testCreateExpirationRecords() {
    $result = civicrm_api3('BroughtForward', 'createexpirationrecords');
    $this->assertEquals(0, $result);

    BroughtForward::create([
      'entitlement_id' => 1,
      'balance' => 2,
      'expiration_date' => date('YmdHis', strtotime('-1 day'))
    ]);

    BroughtForward::create([
      'entitlement_id' => 2,
      'balance' => 5,
      'expiration_date' => date('YmdHis')
    ]);

    BroughtForward::create([
      'entitlement_id' => 3,
      'balance' => 3.5,
      'expiration_date' => date('YmdHis', strtotime('-2 days'))
    ]);

    // Should create two records: one for the entitlement 1 and another one
    // for entitlement 3. The brought forward for entitlement 2 has not expired
    $result = civicrm_api3('BroughtForward', 'createexpirationrecords');
    $this->assertEquals(2, $result);
  }
}
