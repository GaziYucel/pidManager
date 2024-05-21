<?php
/**
 * @file classes/Db/PluginDao.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PluginDAO
 * @brief DAO for plugin
 */

namespace APP\plugins\generic\pidManager\classes\Db;

use DAORegistry;
use Exception;
use JournalDAO;
use PKP\context\Context;

class PluginDao
{
    public function getJournal(int $journalId): ?Context
    {
        try {
            /* @var JournalDAO $dao */
            $dao = DAORegistry::getDAO('JournalDAO');
        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        /* @var Context */
        return $dao->getById($journalId);
    }

    /* OJS setters */
    public function saveJournal(Context $journal): void
    {
        try {
            /* @var JournalDAO $dao */
            $dao = DAORegistry::getDAO('JournalDAO');
            $dao->updateObject($journal);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
}
