{**
 * templates/igsn/workflow.tpl
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

    <div class="pkpFormField__heading">
        <label class="pkpFormFieldLabel">
            {translate key="plugins.generic.pidManager.{$pidName}.workflow.label"}
        </label>
    </div>
    <div class="pkpFormField__description">
        {translate key="plugins.generic.pidManager.{$pidName}.generalDescription"}
        <br>
        {translate key="plugins.generic.pidManager.{$pidName}.workflow.instructions"}
    </div>

    <div class="content">
        <table class="w-full pt-16" :class="{ 'disabled': pidManagerApp{$pidName}.isPublished }">
            <tr>
                <th>
                    <span class="block">
                        {translate key="plugins.generic.pidManager.{$pidName}.workflow.table.pid"}
                    </span>
                </th>
                <th>
                    <span class="block">
                        {translate key="plugins.generic.pidManager.{$pidName}.workflow.table.title"}
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
                    <input v-model="pidManagerApp{$pidName}.searchPhraseTitle" type="text"
                           class="pkpFormField__input pkpFormField--text__input"
                           placeholder="{translate key="plugins.generic.pidManager.{$pidName}.datacite.searchPhraseTitle.placeholder"}"
                    />
                </td>
                <td class="center w-42">
                    <a @click="pidManagerApp{$pidName}.apiLookup()"
                       class="pkpButton h-40 min-w-40 line-height-40">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </a>
                </td>
            </tr>
            <tr v-if="pidManagerApp{$pidName}.panelVisibility">
                <td colspan="2">
                    <div id="pidManagerSearchResults">
                        <span v-if="pidManagerApp{$pidName}.panelVisibility === 'noResult'"
                              class="center w-full inline-block pt-60">
                            {translate key="plugins.generic.pidManager.{$pidName}.datacite.empty"}
                        </span>
                        <span v-else-if="pidManagerApp{$pidName}.panelVisibility === 'loading'"
                              class="pkpSpinner center w-full inline-block pt-60">
                        </span>
                        <table v-else-if="pidManagerApp{$pidName}.panelVisibility === 'result'" class="w-full">
                            <template v-for="(row, j) in pidManagerApp{$pidName}.searchResults">
                                <tr>
                                    <td class="center w-42 p-0">
                                        <a :href="'https://doi.org/' + row.doi" target="_blank">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                    </td>
                                    <td class="p-0">
                                        <a @click="pidManagerApp{$pidName}.select(j)" class="searchRowLink"
                                           :class="{ 'disabled': row.exists }">
                                            <span v-if="row.creators">{{ row.creators }}</span>
                                            <span v-if="row.publicationYear"> ({{ row.publicationYear }}).</span>
                                            <span v-if="row.label"><em>{{ row.label }}</em>.</span>
                                            <span v-if="row.publisher">{{ row.publisher }}.</span>
                                            <span v-if="row.doi">{{ row.doi }}</span>
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
        <table class="w-full" :class="{ 'disabled': pidManagerApp{$pidName}.isPublished }">
            <tr>
                <th>
                    <span class="block">
                        {translate key="plugins.generic.pidManager.{$pidName}.workflow.table.pid"}
                    </span>
                </th>
                <th>
                    <span class="block">
                        {translate key="plugins.generic.pidManager.{$pidName}.workflow.table.title"}
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
                        <a @click="pidManagerApp{$pidName}.remove(i)" class="pkpButton h-40 min-w-40 line-height-40">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>
                    </td>
                </tr>
            </template>
            <tr v-show="!pidManagerApp{$pidName}.items">
                <td colspan="6" class="center w-42 h-42">
                    {translate key="plugins.generic.pidManager.{$pidName}.workflow.empty"}
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <p>
                        <a @click="pidManagerApp{$pidName}.add()" class="pkpButton">
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
            {{ components.{$pidName}.fields[0]['value'] = JSON.stringify(pidManagerApp{$pidName}.items?.filter((item) => Object.values(item).some((value) => value !== null && value.length > 0))) }}
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
                    searchPhraseTitle: '',
                    rawSearchResults: [],
                    panelVisibility: '',
                    pendingRequests: new WeakMap(),
                    workingPublication: { /**/ id: 0},
                    apiUrl: 'https://api.datacite.org/dois?fields[dois]=titles,creators,publisher,publicationYear&query=relatedIdentifiers.relatedIdentifierType:IGSN AND types.resourceTypeGeneral:PhysicalObject'
                };
            },
            computed: {
                isPublished: function () {
                    let isPublished = false;
                    if (pkp.const.STATUS_PUBLISHED === this.workingPublication.status) {
                        isPublished = true;
                    }
                    return isPublished;
                },
                searchResults: function () {
                    let result = [];
                    this.rawSearchResults.forEach((item) => {
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

                                if (item.attributes.creators[i].nameType === 'Personal') {
                                    itemChanged['creators'] +=
                                        item.attributes.creators[i].familyName + ', ' +
                                        item.attributes.creators[i].givenName?.substring(0, 1) + '.';
                                } else {
                                    itemChanged['creators'] += item.attributes.creators[i].name;
                                }
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

                        result.push(itemChanged);
                    });
                    return result;
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
                    this.rawSearchResults = [];
                    this.panelVisibility = '';
                    this.stopPendingRequests();
                },
                stopPendingRequests: function () {
                    const previousController = this.pendingRequests.get(this);
                    if (previousController) previousController.abort();
                },
                apiLookup: function () {
                    const minLength = 3;
                    let doi = this.searchPhraseDoi.trim().replace(/  +/g, ' ');
                    let title = this.searchPhraseTitle.trim().replace(/  +/g, ' ');

                    if (doi.length < minLength && title.length < minLength) {
                        return;
                    }

                    let query = '';
                    if (doi.length >= minLength) {
                        query += ' AND id:' + '*' + doi.replaceAll(' ', '*+*').toLowerCase() + '*';
                    }
                    if (title.length >= minLength) {
                        query += ' AND titles.title:' + '*' + title.replaceAll(' ', '*+*').toLowerCase() + '*';
                    }

                    this.panelVisibility = 'loading';

                    const controller = new AbortController();
                    this.pendingRequests.set(this, controller);

                    fetch(this.apiUrl + encodeURI(query) + '', {
                        signal: controller.signal
                    })
                        .then(response => response.json())
                        .then(responseData => {
                            this.rawSearchResults = responseData.data;
                            if (this.searchResults.length > 0) {
                                this.panelVisibility = 'result';
                            } else {
                                this.panelVisibility = 'noResult';
                            }
                        })
                        .catch(error => {
                            if (error.name === 'AbortError') {
                                return;
                            }
                            console.log(error);
                        });
                },
                select: function (index) {
                    for (let i = 0; i < this.items.length; i++) {
                        if (this.items[i].doi === this.searchResults[index].doi) {
                            return;
                        }
                    }

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
                        this.items = JSON.parse(this.workingPublication['{$pidName}']);
                    }
                }
            }
        });
    </script>
</tab>
