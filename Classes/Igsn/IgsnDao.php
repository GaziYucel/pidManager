<?php
/**
 * @file Classes/IgsnIgsnDao.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class IgsnDao
 * @brief Igsn Dao
 */

namespace APP\plugins\generic\pidManager\Classes\Igsn;

use APP\publication\Publication;
use ReflectionClass;
use ReflectionProperty;

class IgsnDao
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

        $igsnsIn = json_decode($publication->getData(IgsnConstants::igsn), true);

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
