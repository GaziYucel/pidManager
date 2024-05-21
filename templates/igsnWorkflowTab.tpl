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

            <div class="header">
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
                                       class="pkpFormField__input pkpFormField--text__input" />
                            </td>
                            <td>
                                <input v-model="igsn.label" type="text"
                                       class="pkpFormField__input pkpFormField--text__input" />
                            </td>
                            <td>
                                <a @click="pidManagerIgsnApp.searchShow(i)" class="pkpButton">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                </a>
                                <a @click="pidManagerIgsnApp.remove(i)" class="pkpButton">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                        <tr v-if="pidManagerIgsnApp.focusedIndex === i">
                            <td>
                                <div id="pidManagerSearchResultsPhrase">
                                    <table>
                                        <tr>
                                            <td>
                                                <input v-model="pidManagerIgsnApp.searchPhrase" type="text"
                                                       class="pkpFormField__input pkpFormField--text__input"
                                                       placeholder="{translate key="plugins.generic.pidManager.igsn.datacite.searchPhrase.placeholder"}" />
                                            </td>
                                            <td>
                                                <a @click="pidManagerIgsnApp.apiLookup(i)" class="pkpButton">
                                                    <i class="fa fa-search" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                            <td>
                                <div id="pidManagerSearchResults">
                                    <span v-show="pidManagerIgsnApp.searchResultsShow.info">
                                        {translate key="plugins.generic.pidManager.igsn.datacite.info"}
                                    </span>
                                    <span v-show="pidManagerIgsnApp.searchResultsShow.empty">
                                        {translate key="plugins.generic.pidManager.igsn.datacite.empty"}
                                    </span>
                                    <span v-show="pidManagerIgsnApp.searchResultsShow.spinner" aria-hidden="true"
                                          class="pkpSpinner">
                                    </span>
                                    <table v-show="pidManagerIgsnApp.searchResultsShow.list">
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
                            </td>
                            <td>
                                <a @click="pidManagerIgsnApp.searchHide()" class="pkpButton">
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
            <span>{{ pidManagerIgsnApp.workingPublication = workingPublication }}</span>
            <span>{{ components.{PidManagerPlugin::IGSN}.fields[0]['value'] = JSON.stringify(pidManagerIgsnApp.igsnSClean) }}</span>
            <span>{{ components.{PidManagerPlugin::IGSN}.action = '{$apiBaseUrl}submissions/' + workingPublication.submissionId + '/publications/' + workingPublication.id }}</span>
        </div>
        <div>
            <pkp-form v-bind="components.{PidManagerPlugin::IGSN}" @set="set"></pkp-form>
        </div>
    </div>

</tab>

<script>
	let pidManagerIgsnApp = new pkp.Vue({
		data() {
			return {
				resourceTypes: ['dataset'],
				igsnS: [],
				// igsnS: [{ /**/ id: '10.11570/18.0003', label: 'Kinematics of the Atomic ISM in M33 on 80 pc scales'}],
				focusedIndex: -1,
				searchResults: [], // [ { 'id': '', 'label': '' }, ... ]
				searchResultsShow: { /**/ info: true, empty: false, spinner: false, list: false},
				searchPhrase: '',
				igsnModel: { /**/ 'id': '', 'label': ''},
				minimumSearchPhraseLength: 3,
				weakMap: new WeakMap(),
				publication: { /**/ id: 0},
				workingPublication: { /**/ id: 0} // workingPublication
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
				if (pkp.const.STATUS_PUBLISHED === this.workingPublication.status) {
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
			searchShow: function(index) {
				this.focusedIndex = index;
				this.searchReset();
				this.showSearchResultsPart('info');
			},
			searchHide: function() {
				this.focusedIndex = -1;
				this.searchReset();
			},
			searchReset: function() {
				this.searchPhrase = '';
				this.searchResults = [];
				this.resetSearchResultsShow();
				this.stopPending();
			},
			select: function(indexIgsnS, indexSearchResults) {
				this.igsnS[indexIgsnS].id = this.searchResults[indexSearchResults].id;
				this.igsnS[indexIgsnS].label = this.searchResults[indexSearchResults].label;
			},
			stopPending: function() {
				const previousController = this.weakMap.get(this);
				if (previousController) previousController.abort();
			},
			showSearchResultsPart: function(part) {
				this.resetSearchResultsShow();
				this.searchResultsShow[part] = true;
			},
			resetSearchResultsShow: function() {
				Object.keys(this.searchResultsShow).forEach((key) => {
					this.searchResultsShow[key] = false;
				});
			},
			apiLookup: function(index) {
				this.focusedIndex = index;
				this.stopPending();

				if (this.searchPhrase.length < this.minimumSearchPhraseLength) return;

				this.showSearchResultsPart('spinner');

				let searchResults = [];
				const controller = new AbortController();
				this.weakMap.set(this, controller);

				fetch('https://api.datacite.org/dois?query=*' + this.searchPhraseUri + '*', {
					signal: controller.signal
				})
					.then(response => response.json())
					.then(responseData => {
						let items = responseData.data;
						items.forEach((item) => {
							if (this.resourceTypes.includes(
								item.attributes.types['resourceTypeGeneral'].toLowerCase())
							) {
								let label = '';
								for (let i = 0; i < item.attributes.titles.length; i++) {
									label = item.attributes.titles[i].title;
								}
								let row = {
									id: item.id, label: label
								};
								searchResults.push(row);
							}
						});
						this.searchResults = searchResults;

						this.showSearchResultsPart('list');
						if (this.searchResults.length === 0) this.showSearchResultsPart('empty');
					})
					.catch(error => {
						if (error.name === 'AbortError') {
							console.log(error.name);
							return;
						}
						console.log(error);
					});

			}
		},
		watch: {
			workingPublication(newValue, oldValue) {
				if (newValue !== oldValue) {
					this.publication = this.workingPublication;
					console.log('workingPublication: ' + oldValue['id'] + ' > ' + newValue['id']);
				}
			},
			searchResultsShow(newValue, oldValue) {
				console.log('workingPublication: ' + oldValue['id'] + ' > ' + newValue['id']);
			}
		},
		created() {
			if (this.igsnS.length === 0) {
				this.igsnS.push(this.igsnModel);
			}
		}
	});
</script>
