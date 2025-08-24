<?php

/**
 * @file classes/Igsn/IgsnRepo.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class IgsnRepo
 * @brief Igsn Repo
 */

namespace APP\plugins\generic\pidManager\classes\Igsn;

use APP\plugins\generic\pidManager\classes\Constants;
use Publication;
use ReflectionClass;
use ReflectionProperty;

class IgsnRepo
{
  /**
   * This method retrieves the igsns for a publication from the publication object.
   * After this, the method returns an array of IgsnDataModels.
   * If no igsns are found, the method returns an empty array.
   *
   * @param Publication|null $publication
   * @return array
   */
  public function getIgsns(Publication|null $publication): array
  {
    if (empty($publication)) return [];

    $igsnsIn = json_decode($publication->getData(Constants::igsn), true);

    if (empty($igsnsIn) || json_last_error() !== JSON_ERROR_NONE) return [];

    $igsnsOut = [];

    foreach ($igsnsIn as $igsn) {
      if (!empty($igsn) && (is_object($igsn) || is_array($igsn))) {
        $igsnDataModel = new IgsnDataModel();
        $reflect = new ReflectionClass(new IgsnDataModel());
        $properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
          if (!empty($igsn[$property->getName()])) {
            $igsnDataModel->{$property->getName()} = $igsn[$property->getName()];
          }
        }
        $igsnsOut[] = $igsnDataModel;
      }
    }

    return $igsnsOut;
  }
}
