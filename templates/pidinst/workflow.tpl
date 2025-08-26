{**
 * templates/pidinst/workflow.tpl
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Workflow IGSN
 *
 * https://schema.datacite.org/meta/kernel-4.5/
 * https://support.datacite.org/docs/api-queries#selecting-which-metadata-fields-to-retrieve
 *}

<tab id='pidManagerPidinst' role='tabpanel' class='pkpTab'
     label="{translate key='plugins.generic.pidManager.pidinst.workflow.name'}">

    <link rel="stylesheet" href="{$assetsUrl}/css/backend.css" type="text/css"/>

    <div class="header">
        <h4 class="mt-0">{translate key="plugins.generic.pidManager.pidinst.workflow.label"}</h4>
        <span>{translate key="plugins.generic.pidManager.pidinst.workflow.description"}</span>
    </div>

    <div class="content">
        <table class="w-full pt-16">
            <tr>
                <td>
                    <input v-model="pidManagerAppPidinst.searchPhraseDoi" type="text"
                           class="pkpFormField__input pkpFormField--text__input"
                           placeholder="{translate key="plugins.generic.pidManager.pidinst.datacite.searchPhraseDoi.placeholder"}"
                    />
                </td>
                <td>
                    <input v-model="pidManagerAppPidinst.searchPhraseLabel" type="text"
                           class="pkpFormField__input pkpFormField--text__input"
                           placeholder="{translate key="plugins.generic.pidManager.pidinst.datacite.searchPhraseLabel.placeholder"}"
                    />
                </td>
                <td class="center w-42">
                    <a @click="pidManagerAppPidinst.apiLookup()"
                       class="pkpButton h-40 min-w-40 line-height-40"
                       :class="{ 'disabled': pidManagerAppPidinst.isPublished }">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </a>
                </td>
            </tr>
            <tr v-if="pidManagerAppPidinst.showSearchResultsPane">
                <td colspan="2">
                    <div id="pidManagerSearchResults">
                        <span v-if="pidManagerAppPidinst.panelVisibility.empty"
                              class="center w-full inline-block pt-60">
                            {translate key="plugins.generic.pidManager.pidinst.datacite.empty"}
                        </span>
                        <span v-else-if="pidManagerAppPidinst.panelVisibility.spinner"
                              class="pkpSpinner center w-full inline-block pt-60">
                        </span>
                        <table v-else-if="pidManagerAppPidinst.panelVisibility.list" class="w-full">
                            <template v-for="(row, j) in pidManagerAppPidinst.searchResultsFiltered">
                                <tr>
                                    <td class="center w-42 p-0">
                                        <a :href="'https://doi.org/' + row.doi" target="_blank">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                    </td>
                                    <td class="p-0">
                                        <a @click="pidManagerAppPidinst.select(j)" class="searchRowLink"
                                           :class="{ 'disabled': row.exists }">
                                            {{ row.label }} [{{ row.doi }}]
                                        </a>
                                    </td>
                                </tr>
                            </template>
                        </table>
                    </div>
                </td>
                <td class="center w-42">
                    <a @click="pidManagerAppPidinst.clearSearch()" class="pkpButton h-40 min-w-40 line-height-40">
                        <i aria-hidden="true" class="fa fa-times"></i>
                    </a>
                </td>
            </tr>
            <tr v-else>
                <td colspan="3" class="h-42">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <th>
                    <span class="block">
                        {translate key="plugins.generic.pidManager.pidinst.workflow.table.pid"}
                    </span>
                </th>
                <th>
                    <span class="block">
                        {translate key="plugins.generic.pidManager.pidinst.workflow.table.label"}
                    </span>
                </th>
                <th class="center w-42">
                    &nbsp;
                </th>
            </tr>
            <template v-for="(item, i) in pidManagerAppPidinst.items" class="pidManager-Row">
                <tr>
                    <td><input v-model="item.doi" type="text"
                               class="pkpFormField__input pkpFormField--text__input"/>
                    </td>
                    <td><input v-model="item.label" type="text"
                               class="pkpFormField__input pkpFormField--text__input"/>
                    </td>
                    <td class="center w-42">
                        <a @click="pidManagerAppPidinst.remove(i)" class="pkpButton h-40 min-w-40 line-height-40"
                           :class="{ 'disabled': pidManagerAppPidinst.isPublished }">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>
                    </td>
                </tr>
            </template>
            <tr v-show="pidManagerAppPidinst.items.length === 0">
                <td colspan="3" class="center w-42 h-42">
                    {translate key="plugins.generic.pidManager.pidinst.workflow.empty"}
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <p>
                        <a @click="pidManagerAppPidinst.add()" v-show="!pidManagerAppPidinst.isPublished"
                           class="pkpButton">
                            {translate key="plugins.generic.pidManager.pidinst.button.add"}
                        </a>
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <pkp-form v-bind="components.{$pidName}" @set="set"></pkp-form>
        <span class="hide">
            {{ pidManagerAppPidinst.workingPublication = workingPublication }}
            {{ pidManagerAppPidinst.configure() }}
            {{ components.{$pidName}.fields[0]['value'] = JSON.stringify(pidManagerAppPidinst.itemListCleaned) }}
            {{ components.{$pidName}.action = '{$apiBaseUrl}submissions/' + workingPublication.submissionId + '/publications/' + workingPublication.id }}
        </span>
    </div>

    <script>
        let pidManagerAppPidinst = new pkp.Vue({
            data() {
                return {
                    items: {$items},
                    dataModel: { /**/ 'doi': '', 'label': ''},
                    searchPhraseDoi: '',
                    searchPhraseLabel: '',
                    searchResults: [], // [ { 'id': '', 'label': '' }, ... ]
                    panelVisibility: { /**/ empty: false, spinner: false, list: false},
                    panelVisibilityDefault: { /**/ empty: false, spinner: false, list: false},
                    minimumSearchPhraseLength: 3,
                    pendingRequests: new WeakMap(),
                    publication: { /**/ id: 0},
                    workingPublication: { /**/ id: 0}, // workingPublication
                    apiUrl: 'https://api.datacite.org/dois?fields[dois]=titles&query=relatedIdentifiers.relatedIdentifierType:PIDINST AND types.resourceTypeGeneral:Instrument'
                };
            },
            computed: {
                itemListCleaned: function () {
                    let result = JSON.parse(JSON.stringify(this.items));
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
                },
                searchResultsFiltered: function () {
                    this.searchResults.forEach((item) => {
                        for (let i = 0; i < this.items.length; i++) {
                            if (this.items[i].doi === item.doi) {
                                item.exists = true;
                            }
                        }
                    });
                    return this.searchResults;
                },
                showSearchResultsPane: function () {
                    return !!(this.searchResults.length > 0 ||
                        this.panelVisibility.empty ||
                        this.panelVisibility.spinner ||
                        this.panelVisibility.list);

                }
            },
            methods: {
                configure: function () {
                    if (document.querySelector('#pidManagerPidinst button.pkpButton') !== null) {
                        let saveBtn = document.querySelector('#pidManagerPidinst button.pkpButton');
                        saveBtn.disabled = this.isPublished;
                    }
                },
                add: function () {
                    this.items.push(JSON.parse(JSON.stringify(this.dataModel)));
                },
                remove: function (index) {
                    if (!this.items[index].doi && !this.items[index].label) {
                        this.items.splice(index, 1);
                        return;
                    }
                    if (confirm('{translate key="plugins.generic.pidManager.pidinst.remove.confirm"}') === true) {
                        this.items.splice(index, 1);
                    }
                },
                clearSearch: function () {
                    this.searchPhraseDoi = '';
                    this.searchPhraseLabel = '';
                    this.searchResults = [];
                    this.panelVisibilityReset();
                    this.stopPendingRequests();
                },
                stopPendingRequests: function () {
                    const previousController = this.pendingRequests.get(this);
                    if (previousController) previousController.abort();
                },
                panelVisibilityShowPart: function (part) {
                    this.panelVisibility = { /**/ ...this.panelVisibilityDefault};
                    this.panelVisibility[part] = true;
                },
                panelVisibilityReset: function () {
                    this.panelVisibility = { /**/ ...this.panelVisibilityDefault};
                },
                getQueryPart: function (query) {
                    query = query.replace(/[.,\/#!$%^&*;:{ }=\-_`~()—+]/g, ' ');
                    query = query.replace(/\s\s+/g, ' ');
                    query = query.trim();
                    query = query.replaceAll(' ', '*+*');
                    query = '*' + query + '*';
                    return query;
                },
                apiLookup: function () {
                    if (this.searchPhraseDoi.length < this.minimumSearchPhraseLength &&
                        this.searchPhraseLabel.length < this.minimumSearchPhraseLength
                    ) {
                        return;
                    }
                    let query = '';
                    if (this.searchPhraseDoi.length >= this.minimumSearchPhraseLength) {
                        query += ' AND id:' + this.getQueryPart(this.searchPhraseDoi);
                    }
                    if (this.searchPhraseLabel.length >= this.minimumSearchPhraseLength) {
                        query += ' AND titles.title:' + this.getQueryPart(this.searchPhraseLabel);
                    }
                    if (query.length === 0) return;

                    const url = this.apiUrl + query + '';

                    this.panelVisibilityShowPart('spinner');
                    const controller = new AbortController();
                    this.pendingRequests.set(this, controller);

                    fetch(url, {
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
                setSearchResults: function (items) {
                    let searchResults = [];
                    items.forEach((item) => {
                        let label = '';
                        let exists = false;

                        for (let i = 0; i < item.attributes.titles.length; i++) {
                            label = item.attributes.titles[i].title;
                        }

                        for (let i = 0; i < this.items.length; i++) {
                            if (this.items[i].doi === item.id) exists = true;
                        }

                        searchResults.push({ /**/ doi: item.id, label: label, exists: exists});
                    });
                    this.searchResults = searchResults;
                },
                select: function (index) {
                    let newItem = {
                        doi: this.searchResults[index].doi,
                        label: this.searchResults[index].label,
                    };
                    this.items.push(newItem);
                },
            },
            watch: {
                workingPublication(newValue, oldValue) {
                    if (newValue !== oldValue) {
                        this.publication = this.workingPublication;
                    }
                }
            }
        });
    </script>

</tab>
