<?php

class CRM_HRLeaveAndAbsences_BAO_BroughtForward extends CRM_HRLeaveAndAbsences_DAO_BroughtForward {

  /**
   * Create a new BroughtForward based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_HRLeaveAndAbsences_DAO_BroughtForward|NULL
   */
  public static function create($params) {
    $entityName = 'BroughtForward';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new self();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }

  /**
   * Returns the brought forward balance for the given entitlement ID.
   *
   * @param int $entitlementID
   *
   * @return float
   */
  public static function getBalanceForEntitlement($entitlementID) {
    $tableName = self::getTableName();

    $query = "
      SELECT SUM(balance) as balance
      FROM {$tableName}
      WHERE entitlement_id = %1
    ";

    $params = [
      '1' => [$entitlementID, 'Integer']
    ];

    $dao = CRM_Core_DAO::executeQuery($query, $params);
    $dao->fetch();
    return (float)$dao->balance;
  }

  /**
   * Creates a new brought forward record to keep track of expired brought
   * forward days.
   *
   * This method checks every brought forward record with a expiration_date in
   * the past, that still don't have a record for the expired days, and creates it.
   *
   * @return int The number of records created
   */
  public static function createExpirationRecords() {
    $numberOfRecordsCreated = 0;

    $tableName = self::getTableName();

    $query = "
      SELECT entitlement_id, balance, expiration_date
      FROM {$tableName}
      WHERE expiration_date < CURDATE()
      GROUP BY entitlement_id
      HAVING count(*) = 1
    ";

    $dao = CRM_Core_DAO::executeQuery($query);
    while($dao->fetch()) {
      self::create([
        'entitlement_id' => $dao->entitlement_id,
        'balance' => self::getNumberOfExpiredDays($dao->balance, $dao->entitlement_id),
        'expiration_date' => date('YmdHis', strtotime($dao->expiration_date))
      ]);
      $numberOfRecordsCreated++;
    }

    return $numberOfRecordsCreated;
  }

  /**
   * Returns the number of expired brought forward days for the given
   * entitlement ID.
   *
   * Since expired days are represented by a negative balance, this method will
   * return the number of expired days as a negative number. For example, if
   * 2 days have expired, the returned value will be -2.
   *
   * @param float $balance
   * @param int $entitlementID
   *
   * @return float
   */
  private static function getNumberOfExpiredDays($balance, $entitlementID) {
    $numberOfLeavesTaken = self::getNumberOfLeavesTakenForEntitlement($entitlementID);

    if($numberOfLeavesTaken > $balance) {
      return 0;
    }

    $expiredDays = $balance - $numberOfLeavesTaken;
    return $expiredDays * -1;
  }

  /**
   * Returns the number of leaves taken for the Entitlement with the given ID.
   *
   * @todo Return a real number. As we don't have leave request yet, this is just returning 0 for now.
   *
   * @param int $entitlementID
   *
   * @return float
   */
  private static function getNumberOfLeavesTakenForEntitlement($entitlementID) {
    return 0;
  }
}
