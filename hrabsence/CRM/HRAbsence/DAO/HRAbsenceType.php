<?php
/*
+--------------------------------------------------------------------+
| CiviHR version 1.4                                                |
+--------------------------------------------------------------------+
| Copyright CiviCRM LLC (c) 2004-2014                                |
+--------------------------------------------------------------------+
| This file is a part of CiviCRM.                                    |
|                                                                    |
| CiviCRM is free software; you can copy, modify, and distribute it  |
| under the terms of the GNU Affero General Public License           |
| Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
|                                                                    |
| CiviCRM is distributed in the hope that it will be useful, but     |
| WITHOUT ANY WARRANTY; without even the implied warranty of         |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
| See the GNU Affero General Public License for more details.        |
|                                                                    |
| You should have received a copy of the GNU Affero General Public   |
| License and the CiviCRM Licensing Exception along                  |
| with this program; if not, contact CiviCRM LLC                     |
| at info[AT]civicrm[DOT]org. If you have questions about the        |
| GNU Affero General Public License or the licensing of CiviCRM,     |
| see the CiviCRM license FAQ at http://civicrm.org/licensing        |
+--------------------------------------------------------------------+
*/
/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 *
 * Generated from xml/schema/CRM/HRAbsence/HRAbsenceType.xml
 * DO NOT EDIT.  Generated by GenCode.php
 */
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';
class CRM_HRAbsence_DAO_HRAbsenceType extends CRM_Core_DAO
{
  /**
   * static instance to hold the table name
   *
   * @var string
   * @static
   */
  static $_tableName = 'civicrm_hrabsence_type';
  /**
   * static instance to hold the field values
   *
   * @var array
   * @static
   */
  static $_fields = null;
  /**
   * static instance to hold the keys used in $_fields for each field.
   *
   * @var array
   * @static
   */
  static $_fieldKeys = null;
  /**
   * static instance to hold the FK relationships
   *
   * @var string
   * @static
   */
  static $_links = null;
  /**
   * static instance to hold the values that can
   * be imported
   *
   * @var array
   * @static
   */
  static $_import = null;
  /**
   * static instance to hold the values that can
   * be exported
   *
   * @var array
   * @static
   */
  static $_export = null;
  /**
   * static value to see if we should log any modifications to
   * this table in the civicrm_log table
   *
   * @var boolean
   * @static
   */
  static $_log = true;
  /**
   * Unique Absence type ID
   *
   * @var int unsigned
   */
  public $id;
  /**
   * Name of absence type
   *
   * @var string
   */
  public $name;
  /**
   * Negotiated name for the Absence Type
   *
   * @var string
   */
  public $title;
  /**
   *
   * @var boolean
   */
  public $is_active;
  /**
   *
   * @var boolean
   */
  public $allow_credits;
  /**
   * FK to civicrm_option_value.id, that has to be valid, registered activity type.
   *
   * @var int unsigned
   */
  public $credit_activity_type_id;
  /**
   *
   * @var boolean
   */
  public $allow_debits;
  /**
   * FK to civicrm_option_value.id, that has to be valid, registered activity type.
   *
   * @var int unsigned
   */
  public $debit_activity_type_id;
  /**
   * class constructor
   *
   * @access public
   * @return civicrm_hrabsence_type
   */
  function __construct()
  {
    $this->__table = 'civicrm_hrabsence_type';
    parent::__construct();
  }
  /**
   * returns all the column names of this table
   *
   * @access public
   * @return array
   */
  static function &fields()
  {
    if (!(self::$_fields)) {
      self::$_fields = array(
        'id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'required' => true,
        ) ,
        'hrabsence_name' => array(
          'name' => 'name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Name') ,
          'maxlength' => 127,
          'size' => CRM_Utils_Type::HUGE,
          'export' => true,
          'where' => 'civicrm_hrabsence_type.name',
          'headerPattern' => '',
          'dataPattern' => '',
        ) ,
        'hrabsence_title' => array(
          'name' => 'title',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Absence Type Title') ,
          'maxlength' => 127,
          'size' => CRM_Utils_Type::HUGE,
          'export' => true,
          'where' => 'civicrm_hrabsence_type.title',
          'headerPattern' => '',
          'dataPattern' => '',
        ) ,
        'is_active' => array(
          'name' => 'is_active',
          'type' => CRM_Utils_Type::T_BOOLEAN,
          'default' => '1',
        ) ,
        'allow_credits' => array(
          'name' => 'allow_credits',
          'type' => CRM_Utils_Type::T_BOOLEAN,
          'title' => ts('Allow Credits') ,
        ) ,
        'credit_activity_type_id' => array(
          'name' => 'credit_activity_type_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Credit Activity Type ID') ,
          'required' => false,
          'import' => true,
          'where' => 'civicrm_hrabsence_type.credit_activity_type_id',
          'headerPattern' => '/(activity.)?type(.id$)/i',
          'dataPattern' => '',
          'export' => false,
          'default' => 'null',
          'pseudoconstant' => array(
            'optionGroupName' => 'activity_type',
          )
        ) ,
        'allow_debits' => array(
          'name' => 'allow_debits',
          'type' => CRM_Utils_Type::T_BOOLEAN,
          'title' => ts('Allow Debits') ,
          'default' => '1',
        ) ,
        'debit_activity_type_id' => array(
          'name' => 'debit_activity_type_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Debit Activity Type ID') ,
          'required' => false,
          'import' => true,
          'where' => 'civicrm_hrabsence_type.debit_activity_type_id',
          'headerPattern' => '/(activity.)?type(.id$)/i',
          'dataPattern' => '',
          'export' => false,
          'default' => 'null',
          'pseudoconstant' => array(
            'optionGroupName' => 'activity_type',
          )
        ) ,
      );
    }
    return self::$_fields;
  }
  /**
   * Returns an array containing, for each field, the arary key used for that
   * field in self::$_fields.
   *
   * @access public
   * @return array
   */
  static function &fieldKeys()
  {
    if (!(self::$_fieldKeys)) {
      self::$_fieldKeys = array(
        'id' => 'id',
        'name' => 'hrabsence_name',
        'title' => 'hrabsence_title',
        'is_active' => 'is_active',
        'allow_credits' => 'allow_credits',
        'credit_activity_type_id' => 'credit_activity_type_id',
        'allow_debits' => 'allow_debits',
        'debit_activity_type_id' => 'debit_activity_type_id',
      );
    }
    return self::$_fieldKeys;
  }
  /**
   * returns the names of this table
   *
   * @access public
   * @static
   * @return string
   */
  static function getTableName()
  {
    return self::$_tableName;
  }
  /**
   * returns if this table needs to be logged
   *
   * @access public
   * @return boolean
   */
  function getLog()
  {
    return self::$_log;
  }
  /**
   * returns the list of fields that can be imported
   *
   * @access public
   * return array
   * @static
   */
  static function &import($prefix = false)
  {
    if (!(self::$_import)) {
      self::$_import = array();
      $fields = self::fields();
      foreach($fields as $name => $field) {
        if (CRM_Utils_Array::value('import', $field)) {
          if ($prefix) {
            self::$_import['hrabsence_type'] = & $fields[$name];
          } else {
            self::$_import[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_import;
  }
  /**
   * returns the list of fields that can be exported
   *
   * @access public
   * return array
   * @static
   */
  static function &export($prefix = false)
  {
    if (!(self::$_export)) {
      self::$_export = array();
      $fields = self::fields();
      foreach($fields as $name => $field) {
        if (CRM_Utils_Array::value('export', $field)) {
          if ($prefix) {
            self::$_export['hrabsence_type'] = & $fields[$name];
          } else {
            self::$_export[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_export;
  }
}
