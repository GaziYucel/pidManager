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

<tab id='{$pidName}' role='tabpanel' class='pkpTab'
     label="{translate key="plugins.generic.pidManager.{$pidName}.workflow.name"}">

    <link rel="stylesheet" href="{$assetsUrl}/css/backend.css" type="text/css"/>

    <div class="header">
        <h4 class="mt-0">{translate key="plugins.generic.pidManager.{$pidName}.workflow.label"}</h4>
        <span>{translate key="plugins.generic.pidManager.{$pidName}.workflow.description"}</span>
    </div>

    <div class="content">
        <table class="w-full pt-16">
            <tr>
                <th>
                    <span class="block">
                        {translate key="plugins.generic.pidManager.{$pidName}.workflow.table.pid"}
                    </span>
                </th>
                <th>
                    <span class="block">
                        {translate key="plugins.generic.pidManager.{$pidName}.workflow.table.label"}
                    </span>
                </th>
                <th class="center w-42">
                    &nbsp;
                </th>
            </tr>
            <tr>
                <td>
                    <input v-model="pidManagerApp{$pidName}.searchPhraseDoi" type="text"
                           class="pkpFormField__input pkpFormField--text__input"
                           placeholder="{translate key="plugins.generic.pidManager.{$pidName}.datacite.searchPhraseDoi.placeholder"}"
                    />
                </td>
                <td>
                    <input v-model="pidManagerApp{$pidName}.searchPhraseLabel" type="text"
                           class="pkpFormField__input pkpFormField--text__input"
                           placeholder="{translate key="plugins.generic.pidManager.{$pidName}.datacite.searchPhraseLabel.placeholder"}"
                    />
                </td>
                <td class="center w-42">
                    <a @click="pidManagerApp{$pidName}.apiLookup()"
                       class="pkpButton h-40 min-w-40 line-height-40"
                       :class="{ 'disabled': pidManagerApp{$pidName}.isPublished }">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </a>
                </td>
            </tr>
            <tr v-if="pidManagerApp{$pidName}.showSearchResultsPane">
                <td colspan="2">
                    <div id="pidManagerSearchResults">
                        <span v-if="pidManagerApp{$pidName}.panelVisibility.empty"
                              class="center w-full inline-block pt-60">
                            {translate key="plugins.generic.pidManager.{$pidName}.datacite.empty"}
                        </span>
                        <span v-else-if="pidManagerApp{$pidName}.panelVisibility.spinner"
                              class="pkpSpinner center w-full inline-block pt-60">
                        </span>
                        <table v-else-if="pidManagerApp{$pidName}.panelVisibility.list" class="w-full">
                            <template v-for="(row, j) in pidManagerApp{$pidName}.searchResultsFiltered">
                                <tr>
                                    <td class="center w-42 p-0">
                                        <a :href="'https://doi.org/' + row.doi" target="_blank">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                    </td>
                                    <td class="p-0">
                                        <a @click="pidManagerApp{$pidName}.select(j)" class="searchRowLink"
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
                    <a @click="pidManagerApp{$pidName}.clearSearch()" class="pkpButton h-40 min-w-40 line-height-40">
                        <i aria-hidden="true" class="fa fa-times"></i>
                    </a>
                </td>
            </tr>
            <tr v-else>
                <td colspan="3" class="h-42">
                    &nbsp;
                </td>
            </tr>
        </table>
        <table class="w-full">
            <tr>
                <th>
                    <span class="block">
                        {translate key="plugins.generic.pidManager.{$pidName}.workflow.table.pid"}
                    </span>
                </th>
                <th>
                    <span class="block">
                        {translate key="plugins.generic.pidManager.{$pidName}.workflow.table.label"}
                    </span>
                </th>
                <th>
                    <span class="block">
                        {translate key="plugins.generic.pidManager.{$pidName}.workflow.table.creators"}
                    </span>
                </th>
                <th>
                    <span class="block">
                        {translate key="plugins.generic.pidManager.{$pidName}.workflow.table.publisher"}
                    </span>
                </th>
                <th class="w-4rem">
                    <span class="block">
                        {translate key="plugins.generic.pidManager.{$pidName}.workflow.table.publicationYear"}
                    </span>
                </th>
                <th class="center w-42"> &nbsp;</th>
            </tr>
            <template v-for="(item, i) in pidManagerApp{$pidName}.items" class="pidManager-Row">
                <tr>
                    <td><input v-model="item.doi" type="text"
                               class="pkpFormField__input pkpFormField--text__input"/>
                    </td>
                    <td><input v-model="item.label" type="text"
                               class="pkpFormField__input pkpFormField--text__input"/>
                    </td>
                    <td><input v-model="item.creators" type="text"
                               class="pkpFormField__input pkpFormField--text__input"/>
                    </td>
                    <td><input v-model="item.publisher" type="text"
                               class="pkpFormField__input pkpFormField--text__input"/>
                    </td>
                    <td><input v-model="item.publicationYear" type="text"
                               class="pkpFormField__input pkpFormField--text__input"/>
                    </td>
                    <td class="center w-42">
                        <a @click="pidManagerApp{$pidName}.remove(i)" class="pkpButton h-40 min-w-40 line-height-40"
                           :class="{ 'disabled': pidManagerApp{$pidName}.isPublished }">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>
                    </td>
                </tr>
            </template>
            <tr v-show="pidManagerApp{$pidName}.items.length === 0">
                <td colspan="6" class="center w-42 h-42">
                    {translate key="plugins.generic.pidManager.{$pidName}.workflow.empty"}
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <p>
                        <a @click="pidManagerApp{$pidName}.add()" v-show="!pidManagerApp{$pidName}.isPublished"
                           class="pkpButton">
                            {translate key="plugins.generic.pidManager.{$pidName}.button.add"}
                        </a>
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <pkp-form v-bind="components.{$pidName}" @set="set"></pkp-form>
        <span class="hide">
            {{ pidManagerApp{$pidName}.workingPublication = workingPublication }}
            {{ pidManagerApp{$pidName}.configure() }}
            {{ components.{$pidName}.fields[0]['value'] = JSON.stringify(pidManagerApp{$pidName}.itemListCleaned) }}
            {{ components.{$pidName}.action = '{$apiBaseUrl}submissions/' + workingPublication.submissionId + '/publications/' + workingPublication.id }}
        </span>
    </div>

    <script>
        let pidManagerApp{$pidName} = new pkp.Vue({
            data() {
                return {
                    items: {$items},
                    dataModel: {$dataModel},
                    searchPhraseDoi: '',
                    searchPhraseLabel: '',
                    searchResults: [], // [ { 'id': '', 'label': '' }, ... ]
                    panelVisibility: { /**/ empty: false, spinner: false, list: false},
                    panelVisibilityDefault: { /**/ empty: false, spinner: false, list: false},
                    minimumSearchPhraseLength: 3,
                    pendingRequests: new WeakMap(),
                    publication: { /**/ id: 0},
                    workingPublication: { /**/ id: 0}, // workingPublication
                    apiUrl: 'https://api.datacite.org/dois?fields[dois]=titles,creators,publisher,publicationYear&query=types.resourceTypeGeneral:Instrument'
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
                    if (document.querySelector('#{$pidName} button.pkpButton') !== null) {
                        let saveBtn = document.querySelector('#{$pidName} button.pkpButton');
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
                    if (confirm('{translate key="plugins.generic.pidManager.{$pidName}.remove.confirm"}') === true) {
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
                getDoiCleaned: function (doi) {
                    doi = doi.replace(/  +/g, ' ');
                    doi = doi.trim();
                    doi = doi.replaceAll(' ', '*+*');
                    doi = '*' + doi + '*';
                    return doi;
                },
                getLabelCleaned: function (label) {
                    label = label.replace(/[.,\/#!$%^&*;:{ }=\-_`~()—+]/g, ' ');
                    return this.getDoiCleaned(label);
                },
                apiLookup: function () {
                    if (this.searchPhraseDoi.length < this.minimumSearchPhraseLength &&
                        this.searchPhraseLabel.length < this.minimumSearchPhraseLength
                    ) {
                        return;
                    }
                    let query = '';
                    if (this.searchPhraseDoi.length >= this.minimumSearchPhraseLength) {
                        query += ' AND id:' + this.getDoiCleaned(this.searchPhraseDoi);
                    }
                    if (this.searchPhraseLabel.length >= this.minimumSearchPhraseLength) {
                        query += ' AND titles.title:' + this.getLabelCleaned(this.searchPhraseLabel);
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
                        let itemChanged = JSON.parse(JSON.stringify(this.dataModel));

                        itemChanged['doi'] = item.id;

                        if (item.attributes?.titles?.length > 0) {
                            itemChanged['label'] = item.attributes.titles[0].title;
                        }

                        if (item.attributes?.creators?.length > 0) {
                            for (let i = 0; i < item.attributes.creators.length; i++) {
                                if (itemChanged['creators']) {
                                    itemChanged['creators'] += ', ';
                                }
                                itemChanged['creators'] +=
                                    item.attributes.creators[i].familyName + ', ' +
                                    item.attributes.creators[i].givenName.substring(0, 1) + '.';
                            }
                        }

                        itemChanged['publisher'] = item.attributes.publisher;
                        itemChanged['publicationYear'] = item.attributes.publicationYear;

                        itemChanged['exists'] = false;
                        for (let i = 0; i < this.items.length; i++) {
                            if (this.items[i].doi === item.id) {
                                itemChanged['exists'] = true;
                            }
                        }

                        searchResults.push(itemChanged);
                    });
                    this.searchResults = searchResults;
                },
                select: function (index) {
                    let newItem = JSON.parse(JSON.stringify(this.dataModel));
                    Object.keys(newItem).forEach(key => {
                        newItem[key] = this.searchResults[index][key];
                    });
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
