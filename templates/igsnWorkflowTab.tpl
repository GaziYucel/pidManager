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
                        <th class="grid-column column-id" style="width: 30%;">Id</th>
                        <th class="grid-column column-label">Label</th>
                        <th class="grid-column column-action" style="width: 34px;"></th>
                    </tr>
                    <tbody>
                    <template v-for="(igsn, i) in pidManagerIgsnApp.igsnS" class="pidManager-Row">
                        <tr>
                            <td>
                                <input v-model="igsn.id" type="text"
                                       @focusin="pidManagerIgsnApp.apiLookup(i)"
                                       @keyup="pidManagerIgsnApp.apiLookup(i)"
                                       class="pkpFormField__input pkpFormField--text__input">
                            </td>
                            <td>
                                <input v-model="igsn.label" type="text"
                                       @focusin="pidManagerIgsnApp.apiLookup(i)"
                                       @keyup="pidManagerIgsnApp.apiLookup(i)"
                                       class="pkpFormField__input pkpFormField--text__input">
                            </td>
                            <td>
                                <a @click="pidManagerIgsnApp.remove(i)" class="pkpButton">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                        <tr v-if="pidManagerIgsnApp.showSearchResults(i)">
                            <td colspan="2">
                                <div class="pidManagerSearchResults">
                                    <div :id="'pidManager-search-empty-' + i"
                                         v-show="pidManagerIgsnApp.showEmpty"
                                         class="pidManagerSearchResultsEmpty">
                                        <span>{translate key="plugins.generic.pidManager.igsn.datacite.empty"}</span>
                                    </div>
                                    <div :id="'pidManager-search-loading-' + i"
                                         v-show="pidManagerIgsnApp.showSpinner"
                                         class="pidManagerSearchResultsLoading">
                                        <span aria-hidden="true" class="pkpSpinner"></span>
                                    </div>
                                    <div :id="'pidManager-search-results-' + i"
                                         v-show="pidManagerIgsnApp.showList"
                                         class="pidManagerSearchResultsList">
                                        <table>
                                            <tr v-for="(row, j) in pidManagerIgsnApp.searchResults">
                                                <td style="width: 24px;">
                                                    <a :href="'https://doi.org/' + row.id" target="_blank">
                                                        <i class="fa fa-external-link"></i>
                                                    </a>
                                                </td>
                                                <td>
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
	let pidManagerIgsnApp = new pkp.Vue({
		data() {
			return {
				igsnS: [
					{ /**/ 'id': 'igsn1', 'label': 'label1'},
					{ /**/ 'id': 'igsn2', 'label': 'label2'},
					{ /**/ 'id': 'igsn3', 'label': 'label3'},
					{ /**/ 'id': 'igsn4', 'label': 'label4'},
					{ /**/ 'id': '10.11570/18.000', 'label': ''}
				],
				focusedIndex: -1,
				searchResults: [], // [ { 'id': '', 'label': '' }, ... ]
				igsnModel: { /**/ 'id': '', 'label': ''},
				resourceTypes: ['dataset'],
				publication: [],
				publicationId: 0,
				submissionId: 0,                // workingPublication.submissionId
				workingPublication: { /* */},   // workingPublication
				workingPublicationId: 0,        // workingPublication.id
				workingPublicationStatus: 0,    // workingPublication.status
				minimumSearchPhraseLength: 3,
				showEmpty: false,
				showSpinner: false,
				showList: false
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
			apiLookup: function(index) {
				this.focusedIndex = index;
				let id = this.igsnS[index].id;
				let label = this.igsnS[index].label;
				if (id.length > this.minimumSearchPhraseLength || label.length > this.minimumSearchPhraseLength) {
					this.loadingStart();
					this.searchResults = [];
					fetch('https://api.datacite.org/dois?query=doi:' + id + '*' + ' AND ' + 'titles.title:(' + label + '*)')
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

							this.loadingEnd();

							if (this.searchResults.length === 0) {
								this.showEmptySearchResults();
							}
						})
						.catch(error => console.log(error));
				} else {
					this.searchResults = [];
				}
			},
			select: function(indexIgsnS, indexSearchResults) {
				this.igsnS[indexIgsnS].id = this.searchResults[indexSearchResults].id;
				this.igsnS[indexIgsnS].label = this.searchResults[indexSearchResults].label;
				console.log(this.searchResults[indexSearchResults].id + ': ' + this.searchResults[indexSearchResults].label);
			},
			loadingStart: function() {
				this.showEmpty = false;
				this.showSpinner = true;
				this.showList = false;
			},
			loadingEnd: function() {
				this.showEmpty = false;
				this.showSpinner = false;
				this.showList = true;
			},
			showEmptySearchResults: function() {
				this.showEmpty = true;
				this.showSpinner = false;
				this.showList = false;
			},
			showSearchResults: function(index) {
				return this.focusedIndex === index;
			},
			hideSearchResults: function() {
				this.focusedIndex = -1;
			}
		},
		watch: {
			workingPublicationId(newValue, oldValue) {
				if (newValue !== oldValue) {
					this.publicationId = this.workingPublicationId;
					this.publication = this.workingPublication;
					console.log(oldValue + ' > ' + newValue);
				}
			}
		},
		created() {
		}
	});
</script>
