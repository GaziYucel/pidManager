<?php
/**
 * @file classes/Igsn/Igsn.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Igsn
 *
 * Data object representing an igsn.
 */

namespace APP\plugins\generic\pidManager\classes\Igsn;

use DataObject;

class Igsn extends DataObject
{
  /**
   * Get context ID.
   *
   * @return int
   */
  function getContextId(): int
  {
    return $this->getData('contextId');
  }

  /**
   * Set context ID.
   *
   * @param $contextId int
   * @return void
   */
  function setContextId(int $contextId): void
  {
    $this->setData('contextId', $contextId);
  }

  /**
   * Get submission ID.
   *
   * @return int
   */
  function getSubmissionId(): int
  {
    return $this->getData('submissionId');
  }

  /**
   * Set submission ID.
   *
   * @param $submissionId int
   */
  function setSubmissionId(int $submissionId): void
  {
    $this->setData('submissionId', $submissionId);
  }
}
