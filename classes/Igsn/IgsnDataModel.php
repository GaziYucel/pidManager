<?php
/**
 * @file classes/Igsn/IgsnDataModel.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class CitationModel
 * @brief Citations are scholarly documents like journal articles, books, datasets, and theses.
 */

namespace APP\plugins\generic\pidManager\classes\Igsn;

class IgsnDataModel
{
    /** @var string|null The id (e.g. DOI) for the sample. */
    public ?string $id = null;

    /** @var string|null The label of the sample. */
    public ?string $label = null;
}
