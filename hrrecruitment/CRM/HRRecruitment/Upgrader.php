<?php

/**
 * Collection of upgrade steps
 */
class CRM_HRRecruitment_Upgrader extends CRM_HRRecruitment_Upgrader_Base {
  public function onInstall() {
    civicrm_api3('CaseType', 'create', $this->getApplicationCaseTypeParams());
    parent::onInstall();
  }

  /**
   * Sets the weight on "Application" CaseType
   *
   * @return bool
   */
  public function upgrade_1400() {
    $this->ctx->log->info('Applying update 1400');
    CRM_Core_DAO::executeQuery("UPDATE civicrm_case_type SET weight = 7 WHERE name = 'Application'");
    return TRUE;
  }

  /**
   * @return array
   */
  public function getApplicationCaseTypeParams() {
    return [
      'name' => 'Application',
      'title' => 'Application',
      'is_active' => '1',
      'is_reserved' => '1',
      'definition' =>
        [
          'activityTypes' =>
            [
              ['name' => 'Open Case'],
              ['name' => 'Follow up'],
              ['name' => 'Change Case Status'],
              ['name' => 'Assign Case Role'],
              ['name' => 'Link Cases'],
              ['name' => 'Email'],
              ['name' => 'Meeting'],
              ['name' => 'Phone Call'],
              ['name' => 'Comment'],
            ],
          'activitySets' =>
            [
              [
                'name' => 'standard_timeline',
                'label' => 'Standard Timeline',
                'timeline' => '1',
                'activityTypes' =>
                  [
                    [
                      'name' => 'Open Case',
                      'status' => 'Completed',
                    ],
                    [
                      'name' => 'Phone Call',
                      'reference_offset' => '1',
                      'reference_select' => 'newest',
                    ],
                    [
                      'name' => 'Email',
                      'reference_offset' => '2',
                      'reference_select' => 'newest',
                    ],
                    [
                      'name' => 'Meeting',
                      'reference_offset' => '3',
                      'reference_select' => 'newest',
                    ],
                    [
                      'name' => 'Follow up',
                      'reference_offset' => '7',
                      'reference_select' => 'newest',
                    ],
                  ],
              ],
            ],
          'caseRoles' =>
            [
              [
                'name' => 'Recruiting Manager',
                'manager' => '1',
                'creator' => '1',
              ],
            ],
        ],
    ];
  }

}
