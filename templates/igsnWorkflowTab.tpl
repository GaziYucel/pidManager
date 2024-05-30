{**
 * templates/igsnWorkflowTab.tpl
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * IGSN tab
 *
 * https://schema.datacite.org/meta/kernel-4.5/
 * https://support.datacite.org/docs/api-queries#selecting-which-metadata-fields-to-retrieve
 *}

<link rel="stylesheet" href="{$assetsUrl}/css/backend.css" type="text/css"/>
<link rel="stylesheet" href="{$assetsUrl}/css/frontend.css" type="text/css"/>

<tab id="pidManagerIgsn" class="pkpTab" role="tabpanel"
     label="{translate key="plugins.generic.pidManager.igsn.workflow.name"}">

    <div class="header">
        <h4>{translate key="plugins.generic.pidManager.igsn.workflow.label"}</h4>
        <span>{translate key="plugins.generic.pidManager.igsn.workflow.description"}</span>
    </div>

    <div class="content">
        <table>
            <tr>
                <th class="grid-column column1">
                    <span>{translate key="plugins.generic.pidManager.igsn.workflow.table.pid"}</span>
                </th>
                <th class="grid-column column2">
                    <span>{translate key="plugins.generic.pidManager.igsn.workflow.table.label"}</span>
                </th>
                <th class="grid-column column3">
                    &nbsp;
                </th>
            </tr>
            <tbody>
            <template v-for="(igsn, i) in pidManagerIgsnApp.igsnS" class="pidManager-Row">
                <tr>
                    <td class="column1">
                        <input v-model="igsn.id" type="text"
                               class="pkpFormField__input pkpFormField--text__input"/>
                    </td>
                    <td class="column2">
                        <input v-model="igsn.label" type="text"
                               class="pkpFormField__input pkpFormField--text__input"/>
                    </td>
                    <td class="column3">
                        <a @click="pidManagerIgsnApp.apiLookup(i)"
                           class="pkpButton"
                           :class="{ 'pidManager-Disabled': pidManagerIgsnApp.isPublished }">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </a>
                        <a @click="pidManagerIgsnApp.remove(i)" class="pkpButton"
                           :class="{ 'pidManager-Disabled': pidManagerIgsnApp.isPublished }">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>
                    </td>
                </tr>
                <tr v-if="pidManagerIgsnApp.focusedIndex === i">
                    <td class="column1" colspan="2">
                        <div id="pidManagerSearchResults">
							<span v-show="pidManagerIgsnApp.panelVisibility.info">
								{translate key="plugins.generic.pidManager.igsn.datacite.info"}</span>
                            <span v-show="pidManagerIgsnApp.panelVisibility.empty">
								{translate key="plugins.generic.pidManager.igsn.datacite.empty"}</span>
                            <span v-show="pidManagerIgsnApp.panelVisibility.spinner" aria-hidden="true"
                                  class="pkpSpinner"></span>
                            <table v-show="pidManagerIgsnApp.panelVisibility.list">
                                <tr v-for="(row, j) in pidManagerIgsnApp.searchResults">
                                    <td class="column1">
                                        <a :href="'https://doi.org/' + row.id" target="_blank">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                    </td>
                                    <td class="column2">
                                        <a @click.prevent="pidManagerIgsnApp.select(i, j)"
                                           :class="{ 'pidManager-Strikethrough': row.exists }">
                                            {{ row.label }} [{{ row.id }}]
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td class="column3">
                        <a @click="pidManagerIgsnApp.searchClose()" class="pkpButton">
                            <icon icon="times"></icon>
                        </a>
                    </td>
                </tr>
            </template>
            <tr v-show="pidManagerIgsnApp.igsnS.length === 0">
                <td colspan="3">
                    <p>
                        {translate key="plugins.generic.pidManager.igsn.workflow.empty"}
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <p>
                        <a class="pkpButton" v-on:click="pidManagerIgsnApp.add()"
                           v-show="!pidManagerIgsnApp.isPublished">
                            {translate key="plugins.generic.pidManager.igsn.button.add"}
                        </a>
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <pkp-form v-bind="components.{PidManagerPlugin::IGSN}" @set="set"></pkp-form>
        <span class="pidManager-Hide">
        	{{ pidManagerIgsnApp.workingPublication = workingPublication }}
        	{{ components.{PidManagerPlugin::IGSN}.fields[0]['value'] = JSON.stringify(pidManagerIgsnApp.igsnSClean) }}
        	{{ components.{PidManagerPlugin::IGSN}.action = '{$apiBaseUrl}submissions/' + workingPublication.submissionId + '/publications/' + workingPublication.id }}
        	{{ pidManagerIgsnApp.configure() }}
    	</span>
    </div>
</tab>

<script>
    let pidManagerIgsnApp = new pkp.Vue({
        data() {
            return {
                igsnS: {$igsnS},
                focusedIndex: -1,
                searchResults: [], // [ { 'id': '', 'label': '' }, ... ]
                panelVisibility: { /**/ info: true, empty: false, spinner: false, list: false},
                igsnModel: { /**/ 'id': '', 'label': ''},
                minimumSearchPhraseLength: 3,
                pendingRequests: new WeakMap(),
                publication: { /**/ id: 0},
                workingPublication: { /**/ id: 0}, // workingPublication
                apiBaseQuery: '?fields[dois]=titles&query=relatedIdentifiers.relatedIdentifierType:IGSN AND types.resourceTypeGeneral:PhysicalObject'
            };
        },
        computed: {
            igsnSClean: function () {
                let result = JSON.parse(JSON.stringify(this.igsnS));
                for (let i = 0; i < result.length; i++) {
                    let rowIsEmpty = true;
                    for (let key in result[i]) {
                        if (result[i][key] !== null && result[i][key].length > 0) {
                            rowIsEmpty = false;
                        }
                    }
                    if (rowIsEmpty === true) {
                        result.splice(i);
                    }
                }
                return result;
            },
            isPublished: function () {
                let isPublished = false;
                if (pkp.const.STATUS_PUBLISHED === this.workingPublication.status) {
                    isPublished = true;
                }
                return isPublished;
            }
        },
        methods: {
            configure: function () {
                if (document.querySelector('#pidManagerIgsn button.pkpButton') !== null) {
                    let saveBtn = document.querySelector('#pidManagerIgsn button.pkpButton');
                    saveBtn.disabled = this.isPublished;
                }
            },
            add: function () {
                this.igsnS.push(JSON.parse(JSON.stringify(this.igsnModel)));
            },
            remove: function (index) {
                if (confirm('{translate key="plugins.generic.pidManager.igsn.button.remove.confirm"}') !== true) {
                    return;
                }
                this.igsnS.splice(index, 1);
            },
            searchReset: function () {
                this.searchResults = [];
                this.panelVisibilityReset();
                this.stopPendingRequests();
            },
            searchClose: function () {
                this.searchReset();
                this.focusedIndex = -1;
            },
            select: function (indexIgsnS, indexSearchResults) {
                this.igsnS[indexIgsnS].id = this.searchResults[indexSearchResults].id;
                this.igsnS[indexIgsnS].label = this.searchResults[indexSearchResults].label;
            },
            stopPendingRequests: function () {
                const previousController = this.pendingRequests.get(this);
                if (previousController) previousController.abort();
            },
            panelVisibilityShowPart: function (part) {
                Object.keys(this.panelVisibility).forEach((key) => {
                    this.panelVisibility[key] = false;
                });
                this.panelVisibility[part] = true;
            },
            panelVisibilityReset: function () {
                this.panelVisibility = { /**/ info: true, empty: false, spinner: false, list: false};
            },
            apiLookup: function (index) {
                this.searchReset();
                this.focusedIndex = index;
                let query = '';
                if (this.igsnS[index].id.length >= this.minimumSearchPhraseLength) {
                    query += ' AND id:' + this.getQueryPart(this.igsnS[index].id);
                }
                if (this.igsnS[index].label.length >= this.minimumSearchPhraseLength) {
                    query += ' AND titles.title:' + this.getQueryPart(this.igsnS[index].label);
                }
                if (query.length === 0) return;

                this.panelVisibilityShowPart('spinner');

                const controller = new AbortController();
                this.pendingRequests.set(this, controller);

                fetch('https://api.datacite.org/dois' + this.apiBaseQuery + query + '', {
                    signal: controller.signal
                })
                    .then(response => response.json())
                    .then(responseData => {
                        this.setSearchResults(responseData.data);
                        this.panelVisibilityShowPart('list');
                        if (this.searchResults.length === 0) this.panelVisibilityShowPart('empty');
                    })
                    .catch(error => {
                        if (error.name === 'AbortError') return;
                        console.log(error);
                    });
            },
            getQueryPart: function (query) {
                query = query.trim();
                query = query.replace(/[.,\/#!$%^&*;:{}=\-_`~()—+]/g, ' ');
                query = query.replace(/\s\s+/g, ' ');
                query = query.replaceAll(' ', '*+*');
                query = '*' + query + '*';
                return query;
            },
            setSearchResults: function (items) {
                let searchResults = [];
                items.forEach((item) => {
                    let label = '';
                    let exists = false;

                    for (let i = 0; i < item.attributes.titles.length; i++) {
                        label = item.attributes.titles[i].title;
                    }

                    for (let i = 0; i < this.igsnS.length; i++) {
                        if (this.igsnS[i].id === item.id) exists = true;
                    }

                    searchResults.push({ /**/ id: item.id, label: label, exists: exists});
                });
                this.searchResults = searchResults;
            }
        },
        watch: {
            workingPublication(newValue, oldValue) {
                if (newValue !== oldValue) {
                    this.publication = this.workingPublication;
                    console.log('pidManagerIgsn:workingPublication: ' + oldValue['id'] + ' > ' + newValue['id']);
                }
            }
        }
    });
</script>
