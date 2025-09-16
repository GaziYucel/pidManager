{**
 * templates/igsn/workflow.tpl
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Workflow IGSN
 *}

{assign var="apiUrl" value="https://api.datacite.org/dois?fields[dois]=titles,creators,publisher,publicationYear&query=relatedIdentifiers.relatedIdentifierType:IGSN AND types.resourceTypeGeneral:PhysicalObject"}

{include "../plugins/generic/pidManager/templates/base/workflow.tpl" apiUrl=$apiUrl}
