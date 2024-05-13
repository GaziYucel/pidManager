{**
 * templates/igsnWorkflowTab.tpl
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * IGSN tab
 * https://support.datacite.org/docs/api-queries#selecting-which-metadata-fields-to-retrieve
 *}

<link rel="stylesheet" href="{$assetsUrl}/css/backend.css" type="text/css" />
<link rel="stylesheet" href="{$assetsUrl}/css/frontend.css" type="text/css" />

<tab id="igsn" class="pkpTab" role="tabpanel"
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
                        <th class="grid-column column-id">Id</th>
                        <th class="grid-column column-label">Label</th>
                        <th class="grid-column column-action"></th>
                    </tr>
                    <tbody>
                    <tr v-for="(igsn, i) in pidManagerIgsnApp.igsnS" class="pidManager-Row">
                        <td>
                            <input v-model="igsn.id" type="text"
                                   @keyup="pidManagerIgsnApp.apiLookupId(i)"
                                   class="pkpFormField__input pkpFormField--text__input">
                        </td>
                        <td>
                            <input v-model="igsn.label" type="text"
                                   @keyup="pidManagerIgsnApp.apiLookupLabel(i)"
                                   class="pkpFormField__input pkpFormField--text__input">
                        </td>
                        <td>
                            <a @click="pidManagerIgsnApp.remove(i)" class="pkpButton">
                                <i class="fa fa-trash" aria-hidden="true"></i> </a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <a class="pkpButton" v-on:click="pidManagerIgsnApp.add()">Add</a>
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

    <div class="debug pidManager-Hide">
        <textarea style="width: 100%; height: 100px;">{{ pidManagerIgsnApp.igsnS }}</textarea>
        <textarea style="width: 100%; height: 100px;">{{ pidManagerIgsnApp.igsnSClean }}</textarea>
    </div>

    <div>
        dataCiteSearchResults
        <hr />
        <div v-for="(row, i) in pidManagerIgsnApp.dataCiteSearchResults">{{ row.label }} ({{ row.id }})</div>
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
					{ /**/ 'id': 'igsn5', 'label': 'label5'}
				],
				dataCiteSearchResults: [], // [ { 'id': '', 'label': '' }, ... ]
				igsnModel: { /**/ 'id': '', 'label': ''},
				searchPhrase: '',
				publication: [],
				publicationId: 0,
				submissionId: 0,                // workingPublication.submissionId
				workingPublication: { /* */},   // workingPublication
				workingPublicationId: 0,        // workingPublication.id
				workingPublicationStatus: 0,    // workingPublication.status
				minimumSearchPhraseLength: 3
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
				if (confirm('Are you sure you want to remove?') !== true) return;

				this.igsnS.splice(index, 1);
			},
			apiLookupId: function(index) {
				let id = this.igsnS[index].id;
				if (id.length > this.minimumSearchPhraseLength) {
					fetch('https://api.datacite.org/dois?query=doi:' + id + '*')
						.then(response => response.json())
						.then(responseData => {
							this.dataCiteSearchResults = [];
							let items = responseData.data;
							items.forEach((item) => {
								// todo: check if type is sample
								let label = '';
								for (let i = 0; i < item.attributes.titles.length; i++) {
									label = item.attributes.titles[i].title;
								}
								let row = {
									id: item.id,
									label: label
								};

								this.dataCiteSearchResults.push(row);
							});
						})
						.catch(error => console.log(error));
				}
			},
			apiLookupLabel: function(index) {
				let label = this.igsnS[index].label;
				if (label.length > this.minimumSearchPhraseLength) {
					fetch('https://api.datacite.org/dois?query=titles.title:(' + label + '*)')
						.then(response => response.json())
						.then(responseData => {
							this.dataCiteSearchResults = [];
							let items = responseData.data;
							items.forEach((item) => {
								// todo: check if type is sample
								let label = '';
								for (let i = 0; i < item.attributes.titles.length; i++) {
									label = item.attributes.titles[i].title;
								}
								let row = {
									id: item.id,
									label: label
								};

								this.dataCiteSearchResults.push(row);
							});
						})
						.catch(error => console.log(error));
				}
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
