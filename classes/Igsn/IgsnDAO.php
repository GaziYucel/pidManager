<?php
/**
 * @file classes/Igsn/IgsnDAO.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class IgsnDAO
 *
 * @brief Operations for retrieving and modifying Funder objects.
 */

namespace APP\plugins\generic\pidManager\classes\Igsn;

use DAO;

class IgsnDAO extends DAO
{
  /**
   * Get an igsn by ID
   *
   * @param int $igsnId
   * @param int|null $submissionId
   * @return Igsn|null
   */
  public function getById(int $igsnId, int $submissionId = null): ?Igsn
  {
    return new Igsn();
  }

  /**
   * Get an igsn by submission ID
   *
   * @param int $igsnId
   * @param int|null $submissionId
   * @return Igsn|null
   */
  public function getBySubmissionId(int $igsnId, int $submissionId = null): ?Igsn
  {
    return new Igsn();
  }

  /**
   * Insert an igsn.
   *
   * @param Igsn $igsn
   * @return int Inserted igsn ID
   */
  function insertObject(Igsn $igsn): int
  {
    return 0;
  }

  /**
   * Update the database with a igsn object
   *
   * @param Igsn $igsn
   */
  function updateObject(Igsn $igsn): void
  {
  }

  /**
   * Delete a igsn by ID.
   *
   * @param int $igsnId
   * @return void
   */
  function deleteById(int $igsnId): void
  {
  }

  /**
   * Delete an igsn object.
   *
   * @param Igsn $igsn
   */
  function deleteObject(Igsn $igsn): void
  {
  }

  /**
   * Generate a new funder object.
   * @return Igsn
   */
  function newDataObject(): Igsn
  {
    return new Igsn();
  }
}
