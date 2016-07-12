<?php

use Civi\Test\HeadlessInterface;
use Civi\Test\TransactionalInterface;
use CRM_HRLeaveAndAbsences_BAO_BroughtForward as BroughtForward;

/**
 * Class CRM_HRLeaveAndAbsences_BAO_BroughtForwardTest
 *
 * @group headless
 */
class CRM_HRLeaveAndAbsences_BAO_BroughtForwardTest extends PHPUnit_Framework_TestCase implements
  HeadlessInterface, TransactionalInterface {

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

  public function testTheBalanceForAnEntitlementWithoutBroughtForwardShouldBeZero() {
    $this->assertEquals(0, BroughtForward::getBalanceForEntitlement(1));
  }

  public function testTheBalanceForAnEntitlementShouldBeTheSumOfItsPositiveAndNegativeBalance() {
    $this->assertEquals(0, BroughtForward::getBalanceForEntitlement(1));

    BroughtForward::create([
      'entitlement_id' => 1,
      'balance' => 5
    ]);
    $this->assertEquals(5, BroughtForward::getBalanceForEntitlement(1));

    BroughtForward::create([
      'entitlement_id' => 1,
      'balance' => -2.5
    ]);
    $this->assertEquals(2.5, BroughtForward::getBalanceForEntitlement(1));
  }

  public function testCreateExpirationRecordsDoesNotCreateRecordsForNonExpiredBroughtForward() {
    BroughtForward::create([
      'entitlement_id' => 1,
      'balance' => 2,
      'expiration_date' => date('YmdHis')
    ]);
    $this->assertEquals(2, BroughtForward::getBalanceForEntitlement(1));

    $numberOfCreatedRecords = BroughtForward::createExpirationRecords();
    $this->assertEquals(0, $numberOfCreatedRecords);
    $this->assertEquals(2, BroughtForward::getBalanceForEntitlement(1));
  }

  public function testCreateExpirationRecordsDoesNotCreateRecordsForBroughtForwardThatNeverExpires() {
    BroughtForward::create([
      'entitlement_id' => 1,
      'balance' => 7,
      'expiration_date' => null //null means it never expires
    ]);
    $this->assertEquals(7, BroughtForward::getBalanceForEntitlement(1));

    $numberOfCreatedRecords = BroughtForward::createExpirationRecords();
    $this->assertEquals(0, $numberOfCreatedRecords);
    $this->assertEquals(7, BroughtForward::getBalanceForEntitlement(1));
  }

  public function testCreateExpirationRecordsDoesNotCreateRecordsForExpiredBroughtForwardWithNegativeBalanceRecord() {
    BroughtForward::create([
      'entitlement_id' => 1,
      'balance' => 2,
      'expiration_date' => date('YmdHis', strtotime('-1 day'))
    ]);
    BroughtForward::create([
      'entitlement_id' => 1,
      'balance' => -1,
      'expiration_date' => date('YmdHis', strtotime('-1 day'))
    ]);
    $this->assertEquals(1, BroughtForward::getBalanceForEntitlement(1));

    $numberOfCreatedRecords = BroughtForward::createExpirationRecords();
    $this->assertEquals(0, $numberOfCreatedRecords);
    $this->assertEquals(1, BroughtForward::getBalanceForEntitlement(1));
  }

  public function testCreateExpirationRecordsDoesCreatesRecordsForExpiredBroughtForward()
  {
    BroughtForward::create([
      'entitlement_id' => 1,
      'balance' => 3,
      'expiration_date' => date('YmdHis', strtotime('-2 days'))
    ]);
    $this->assertEquals(3, BroughtForward::getBalanceForEntitlement(1));

    $numberOfCreatedRecords = BroughtForward::createExpirationRecords();
    $this->assertEquals(1, $numberOfCreatedRecords);
    // as we don't have leave requests for now, all of the brought forward should
    // expire
    $this->assertEquals(0, BroughtForward::getBalanceForEntitlement(1));
  }
}
