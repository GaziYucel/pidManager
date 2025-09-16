{**
 * templates/pidinst/workflow.tpl
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Workflow IGSN
 *}

{assign var="pidName" value="pidinst"}
{assign var="apiUrl" value="https://api.datacite.org/dois?fields[dois]=titles,creators,publisher,publicationYear&query=types.resourceTypeGeneral:Instrument"}

{include "../plugins/generic/pidManager/templates/base/workflow.tpl" pidName=$pidName apiUrl=$apiUrl}
