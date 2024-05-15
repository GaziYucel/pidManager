{**
 * templates/igsnWorkflowTab.tpl
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * IGSN tab
 * https://schema.datacite.org/meta/kernel-4.5/
 * https://support.datacite.org/docs/api-queries#selecting-which-metadata-fields-to-retrieve
 *}

<link rel="stylesheet" href="{$assetsUrl}/css/backend.css" type="text/css" />
<link rel="stylesheet" href="{$assetsUrl}/css/frontend.css" type="text/css" />

<tab id="pidManagerIgsn" class="pkpTab" role="tabpanel"
     label="{translate key="plugins.generic.pidManager.igsn.workflow.label"}">

    <div id="representations-grid" class="">

        <div class="pkp_controllers_grid">

            <div class="header" style="max-height: unset;">
                <h4>{translate key="plugins.generic.pidManager.igsn.workflow.label"}</h4><br>
                <span>{translate key="plugins.generic.pidManager.igsn.workflow.description"}</span>
            </div>

            <div class="content">
                <table>
                    <tr>
                        <th class="grid-column column1">Id</th>
                        <th class="grid-column column2">Label</th>
                        <th class="grid-column column3"></th>
                    </tr>
                    <tbody>
                    <template v-for="(igsn, i) in pidManagerIgsnApp.igsnS" class="pidManager-Row">
                        <tr>
                            <td>
                                <input v-model="igsn.id" type="text"
                                        {*                                       @focusin="pidManagerIgsnApp.search(i)"*}
                                       class="pkpFormField__input pkpFormField--text__input" />
                            </td>
                            <td>
                                <input v-model="igsn.label" type="text"
                                        {*                                       @focusin="pidManagerIgsnApp.search(i)"*}
                                       class="pkpFormField__input pkpFormField--text__input" />
                            </td>
                            <td>
                                <a @click="pidManagerIgsnApp.search(i)" class="pkpButton">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                </a>
                                <a @click="pidManagerIgsnApp.remove(i)" class="pkpButton">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                        <tr v-if="pidManagerIgsnApp.focusedIndex === i">
                            <td colspan="2">
                                <div id="pidManagerSearchResultsPhrase">
                                    <input v-model="pidManagerIgsnApp.searchPhrase" type="text"
                                           @keyup="pidManagerIgsnApp.apiLookup()"
                                           class="pkpFormField__input pkpFormField--text__input"
                                           placeholder="{translate key="plugins.generic.pidManager.igsn.datacite.searchPhrase.placeholder"}" />
                                </div>
                                <div id="pidManagerSearchResults">
                                    <div id="pidManagerSearchResultsInfo"
                                         v-show="pidManagerIgsnApp.searchResultsShow.info">
                                        <span>{translate key="plugins.generic.pidManager.igsn.datacite.info"}</span>
                                    </div>
                                    <div id="pidManagerSearchResultsEmpty"
                                         v-show="pidManagerIgsnApp.searchResultsShow.empty">
                                        <span>{translate key="plugins.generic.pidManager.igsn.datacite.empty"}</span>
                                    </div>
                                    <div id="pidManagerSearchResultsSpinner"
                                         v-show="pidManagerIgsnApp.searchResultsShow.spinner">
                                        <span aria-hidden="true" class="pkpSpinner"></span>
                                    </div>
                                    <div id="pidManagerSearchResultsList"
                                         v-show="pidManagerIgsnApp.searchResultsShow.list">
                                        <table>
                                            <tr v-for="(row, j) in pidManagerIgsnApp.searchResults">
                                                <td class="column1">
                                                    <a :href="'https://doi.org/' + row.id" target="_blank">
                                                        <i class="fa fa-external-link"></i>
                                                    </a>
                                                </td>
                                                <td class="column2">
                                                    <a @click.prevent="pidManagerIgsnApp.select(i, j)">
                                                        {{ row.label }} [{{ row.id }}]
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a @click="pidManagerIgsnApp.hideSearchResults()" class="pkpButton">
                                    <icon icon="times"></icon>
                                </a>
                            </td>
                        </tr>
                    </template>
                    <tr>
                        <td colspan="3">
                            <a class="pkpButton" v-on:click="pidManagerIgsnApp.add()">
                                {translate key="plugins.generic.pidManager.igsn.button.add"}
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <div>
        <div class="pidManager-Hide">
            <span>{{ pidManagerIgsnApp.workingPublication         = workingPublication }}</span>
            <span>{{ pidManagerIgsnApp.workingPublicationStatus   = workingPublication.status }}</span>
            <span>{{ pidManagerIgsnApp.submissionId               = workingPublication.submissionId }}</span>
            <span>{{ pidManagerIgsnApp.workingPublicationId       = workingPublication.id }}</span>
            {* <span>{{ components.{PidManagerPlugin::IGSN}.fields[0]['value'] = JSON.stringify(pidManagerIgsnApp.igsnClean) }}</span> *}
            {* <span>{{ components.{PidManagerPlugin::IGSN}.action = '{$apiBaseUrl}submissions/' + workingPublication.submissionId + '/publications/' + workingPublication.id }}</span> *}
        </div>
        <div>
            {* <pkp-form v-bind="components.{PidManagerPlugin::IGSN}" @set="set"></pkp-form> *}
        </div>
    </div>

</tab>

<script>
	let pidManagerIgsnController = new AbortController();

	let pidManagerIgsnApp = new pkp.Vue({
		data() {
			return {
				resourceTypes: ['dataset'],
				igsnS: [], // [{ /**/ id: '10.11570/18.0003', label: 'Kinematics of the Atomic ISM in M33 on 80 pc scales'}],
				igsnModel: { /**/ 'id': '', 'label': ''},
				minimumSearchPhraseLength: 3,
				focusedIndex: -1,
				searchResults: [], // [ { 'id': '', 'label': '' }, ... ]
				searchResultsShow: { /**/ info: true, empty: false, spinner: false, list: false},
				searchPhrase: '',
				publication: [],
				publicationId: 0,
				submissionId: 0,              // workingPublication.submissionId
				workingPublication: { /* */}, // workingPublication
				workingPublicationId: 0,      // workingPublication.id
				workingPublicationStatus: 0   // workingPublication.status
			};
		},
		computed: {
			igsnSClean: function() {
				let result = JSON.parse(JSON.stringify(this.igsnS));
				for (let i = 0; i < result.length; i++) {
					let rowIsNull = true;
					for (let key in result[i]) {
						if (result[i][key] !== null && result[i][key].length > 0) {
							rowIsNull = false;
						}
					}
					if (rowIsNull === true) {
						result.splice(i);
					}
				}
				return result;
			},
			isPublished: function() {
				let isPublished = false;
				if (pkp.const.STATUS_PUBLISHED === this.workingPublicationStatus) {
					isPublished = true;
				}
				return isPublished;
			},
			searchPhraseUri: function() {
				let searchPhrase = this.searchPhrase;
				searchPhrase = searchPhrase.trim();
				searchPhrase = searchPhrase.replace(/\s\s+/g, ' ');
				searchPhrase = searchPhrase.replaceAll(' ', '*+*');
				return searchPhrase;
			},
			searchResultsUnique: function() {
				return [...new Map(this.searchResults.map(v => [JSON.stringify(v), v])).values()];
			}
		},
		methods: {
			add: function() {
				this.igsnS.push(JSON.parse(JSON.stringify(this.igsnModel)));
			},
			remove: function(index) {
				if (confirm('{translate key="plugins.generic.pidManager.igsn.button.remove.confirm"}') !== true) {
					return;
				}

				this.igsnS.splice(index, 1);
			},
			search: function(index) {
				this.focusedIndex = index;
				this.searchPhrase = '';
				this.searchResults = [];
				// pidManagerIgsnController.abort();

			},
			apiLookup: function() {
				if (this.searchPhrase.length >= this.minimumSearchPhraseLength) {
					this.searchResults = [];
					this.showSearchResultsPart('spinner');

					fetch('https://api.datacite.org/dois?query=*' + this.searchPhraseUri + '*', {
						signal: pidManagerIgsnController.signal
					})
						.then(response => response.json())
						.then(responseData => {
							let items = responseData.data;
							items.forEach((item) => {
								if (this.resourceTypes.includes(item.attributes.types['resourceTypeGeneral'].toLowerCase())) {
									let label = '';
									for (let i = 0; i < item.attributes.titles.length; i++) {
										label = item.attributes.titles[i].title;
									}
									let row = {
										id: item.id,
										label: label
									};

									this.searchResults.push(row);
								}
							});
							this.showSearchResultsPart('list');

							if (this.searchResults.length === 0) {
								this.showSearchResultsPart('empty');
							}
						})
						.catch(error => console.log(error));
				}
			},
			select: function(indexIgsnS, indexSearchResults) {
				this.igsnS[indexIgsnS].id = this.searchResults[indexSearchResults].id;
				this.igsnS[indexIgsnS].label = this.searchResults[indexSearchResults].label;
				console.log(indexIgsnS + '|' + indexSearchResults + '|' + this.searchResults[indexSearchResults].label);

			},
			resetSearchResultsShow: function() {
				Object.keys(this.searchResultsShow).forEach((key) => {
					this.searchResultsShow[key] = false;
				});
			},
			showSearchResultsPart: function(part) {
				this.resetSearchResultsShow();
				this.searchResultsShow[part] = true;
			},
			hideSearchResults: function() {
				this.focusedIndex = -1;
				this.searchPhrase = '';
				this.searchResults = [];
				pidManagerIgsnController.abort();
			}
		},
		watch: {
			workingPublicationId(newValue, oldValue) {
				if (newValue !== oldValue) {
					this.publicationId = this.workingPublicationId;
					this.publication = this.workingPublication;
					// console.log('workingPublicationId: ' + oldValue + ' > ' + newValue);
				}
			},
			focusedIndex(newValue, oldValue) {
				if (newValue !== oldValue) {
					// console.log('focusedIndex: ' + oldValue + ' > ' + newValue);
				}
			}
		},
		created() {
		}
	});
</script>
