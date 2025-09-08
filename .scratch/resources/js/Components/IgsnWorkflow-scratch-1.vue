<template>
	<div id="igsn" role="tabpanel" class="pkpTab">
		<div class="header">
			<h4 class="mt-0">
				{{ t('plugins.generic.pidManager.igsn.workflow.label') }}
			</h4>
			<span>
				{{ t('plugins.generic.pidManager.igsn.workflow.description') }}
			</span>
		</div>

		<div class="content">
			<!-- search -->
			<table class="pkpTable my-4 w-full">
				<tr>
					<th>
						{{ t('plugins.generic.pidManager.igsn.workflow.table.pid') }}
					</th>
					<th>
						{{ t('plugins.generic.pidManager.igsn.workflow.table.label') }}
					</th>
					<th>&nbsp;</th>
				</tr>
				<tr>
					<td>
						<input
							v-model="searchPhraseDoi"
							type="text"
							class="pkpFormField__input w-full"
							:placeholder="
								t(
									'plugins.generic.pidManager.igsn.datacite.searchPhraseDoi.placeholder',
								)
							"
						/>
					</td>
					<td>
						<input
							v-model="searchPhraseLabel"
							type="text"
							class="pkpFormField__input w-full"
							:placeholder="
								t(
									'plugins.generic.pidManager.igsn.datacite.searchPhraseLabel.placeholder',
								)
							"
						/>
					</td>
					<td class="w-42px">
						<PkpButton
							@click="apiLookup"
							class="h-40px min-w-40px"
							:class="{disabled: isPublished}"
						>
							<i class="fa fa-search" aria-hidden="true"></i>
						</PkpButton>
					</td>
				</tr>
				<tr>
					<td colspan="3">panelVisibility: {{ panelVisibility }}</td>
				</tr>
				<!-- results pane -->
				<tr v-if="showSearchResultsPane">
					<td colspan="2">
						<div id="pidManagerSearchResults">
							<span
								v-if="panelVisibility === 'noResult'"
								class="center inline-block w-full pt-[60px]"
							>
								{{ t('plugins.generic.pidManager.igsn.datacite.empty') }}
							</span>
							<span
								v-else-if="panelVisibility === 'loading'"
								class="pkpSpinner center inline-block w-full pt-60px"
							></span>
							<table v-else-if="panelVisibility === 'result'" class="pkpTable w-full">
								<template
									v-for="(row, j) in searchResultsFiltered"
									:key="row.doi"
								>
									<tr>
										<td class="p-0  w-42px center">
											<a :href="'https://doi.org/' + row.doi"
												 class="line-height-42px block cursor-pointer"
												 target="_blank">
												<i class="fa fa-external-link"></i>
											</a>
										</td>
										<td class="p-0">
											<a
												@click="select(j)"
												:class="{disabled: row.exists}"
												class="line-height-42px block cursor-pointer"
											>
												{{ row.label }} [{{ row.doi }}]
											</a>
										</td>
									</tr>
								</template>
							</table>
						</div>
					</td>
					<td class="w-42px">
						<PkpButton
							@click="clearSearch"
							class="pkpButton line-height-40px h-40px min-w-40px"
						>
							<i aria-hidden="true" class="fa fa-times"></i>
						</PkpButton>
					</td>
				</tr>
			</table>

			<div class="h-42px">&nbsp;</div>

			<!-- items -->
			<table class="pkpTable w-full">
				<tr>
					<th>
						<span>
							{{ t('plugins.generic.pidManager.igsn.workflow.table.pid') }}
						</span>
					</th>
					<th>
						<span>
							{{ t('plugins.generic.pidManager.igsn.workflow.table.label') }}
						</span>
					</th>
					<th>
						<span>
							{{ t('plugins.generic.pidManager.igsn.workflow.table.creators') }}
						</span>
					</th>
					<th>
						<span>
							{{
								t('plugins.generic.pidManager.igsn.workflow.table.publisher')
							}}
						</span>
					</th>
					<th>
						<span>
							{{
								t(
									'plugins.generic.pidManager.igsn.workflow.table.publicationYear',
								)
							}}
						</span>
					</th>
					<th class="center w-42px">&nbsp;</th>
				</tr>

				<template v-for="(item, i) in items" :key="i">
					<tr>
						<td>
							<input
								v-model="item.doi"
								type="text"
								class="pkpFormField__input w-full"
							/>
						</td>
						<td>
							<input
								v-model="item.label"
								type="text"
								class="pkpFormField__input w-full"
							/>
						</td>
						<td>
							<input
								v-model="item.creators"
								type="text"
								class="pkpFormField__input w-full"
							/>
						</td>
						<td>
							<input
								v-model="item.publisher"
								type="text"
								class="pkpFormField__input w-full"
							/>
						</td>
						<td class="w-5rem">
							<input
								v-model="item.publicationYear"
								type="text"
								class="pkpFormField__input w-full"
							/>
						</td>
						<td class="center w-42px">
							<PkpButton
								@click="remove(i)"
								class="pkpButton h-40px min-w-40px"
								:class="{disabled: isPublished}"
							>
								<i class="fa fa-trash" aria-hidden="true"></i>
							</PkpButton>
						</td>
					</tr>
				</template>

				<tr v-show="items.length === 0">
					<td colspan="6" class="center h-42px w-42px">
						{{ t('plugins.generic.pidManager.igsn.workflow.empty') }}
					</td>
				</tr>

				<tr>
					<td colspan="6">
						<p>
							<a @click="add" v-show="!isPublished" class="pkpButton">
								{{ t('plugins.generic.pidManager.igsn.button.add') }}
							</a>
						</p>
					</td>
				</tr>
			</table>
		</div>

		<div class="footer">
			<!-- <pkp-form v-bind="components.igsn" @set="set"></pkp-form>-->
		</div>
	</div>
</template>

<script>
import PkpButton from '@/components/Button/Button.vue';

const {useLocalize} = pkp.modules.useLocalize;
const {t} = useLocalize();

export default {
	name: 'igsnApp',
	components: {
		PkpButton,
	},
	props: {
		submission: {type: Object, required: true},
	},
	data() {
		return {
			items: [],
			dataModel: {
				doi: '',
				label: '',
				creators: '',
				publisher: '',
				publicationYear: '',
			},
			searchPhraseDoi: '',
			searchPhraseLabel: '',
			searchResults: [], // [ { 'id': '', 'label': '' }, ... ]
			panelVisibility: '', // '', 'noResult', 'loading', 'result'
			// panelVisibilityDefault: {/**/ empty: false, loading: false, list: false},
			minimumSearchPhraseLength: 3,
			pendingRequests: new WeakMap(),
			currentPublication: {},
			apiUrl:
				'https://api.datacite.org/dois?fields[dois]=titles,creators,publisher,publicationYear&query=relatedIdentifiers.relatedIdentifierType:IGSN AND types.resourceTypeGeneral:PhysicalObject',
		};
	},
	methods: {
		t,
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
			if (
				confirm(
					'{translate key="plugins.generic.pidManager.{$pidName}.remove.confirm"}',
				) === true
			) {
				this.items.splice(index, 1);
			}
		},
		clearSearch: function () {
			this.searchPhraseDoi = '';
			this.searchPhraseLabel = '';
			this.searchResults = [];
			this.panelVisibility = '';
			this.stopPendingRequests();
		},
		stopPendingRequests: function () {
			const previousController = this.pendingRequests.get(this);
			if (previousController) previousController.abort();
		},
		// panelVisibilityShowPart: function (part) {
		// 	this.panelVisibility = {/**/ ...this.panelVisibilityDefault};
		// 	this.panelVisibility[part] = true;
		// },
		// panelVisibilityReset: function () {
		// 	this.panelVisibility = {/**/ ...this.panelVisibilityDefault};
		// },
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
			if (
				this.searchPhraseDoi.length < this.minimumSearchPhraseLength &&
				this.searchPhraseLabel.length < this.minimumSearchPhraseLength
			) {
				return;
			}
			let query = '';
			if (this.searchPhraseDoi.length >= this.minimumSearchPhraseLength) {
				query += ' AND id:' + this.getDoiCleaned(this.searchPhraseDoi);
			}
			if (this.searchPhraseLabel.length >= this.minimumSearchPhraseLength) {
				query +=
					' AND titles.title:' + this.getLabelCleaned(this.searchPhraseLabel);
			}
			if (query.length === 0) return;

			const url = this.apiUrl + query + '';

			this.panelVisibility = 'loading';
			const controller = new AbortController();
			this.pendingRequests.set(this, controller);

			fetch(url, {
				signal: controller.signal,
			})
				.then((response) => response.json())
				.then((responseData) => {
					this.setSearchResults(responseData.data);
					this.panelVisibility = 'result';
					if (this.searchResults.length === 0)
						this.panelVisibility = 'noResult';
				})
				.catch((error) => {
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

						if (item.attributes.creators[i].nameType === 'Personal') {
							itemChanged['creators'] +=
								item.attributes.creators[i].familyName +
								', ' +
								item.attributes.creators[i].givenName?.substring(0, 1) +
								'.';
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

				searchResults.push(itemChanged);
			});
			this.searchResults = searchResults;
			console.log('setSearchResults: this.searchResults', this.searchResults);
		},
		select: function (index) {
			let newItem = JSON.parse(JSON.stringify(this.dataModel));
			Object.keys(newItem).forEach((key) => {
				newItem[key] = this.searchResults[index][key];
			});
			this.items.push(newItem);
		},
	},
	watch: {
		// currentPublication(newValue, oldValue) {
		// 	if (newValue !== oldValue) {
		// 		this.publication = this.currentPublication;
		// 	}
		// },
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
			if (pkp.const.STATUS_PUBLISHED === this.currentPublication.status) {
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
			console.log(
				'searchResultsFiltered: this.searchResults',
				this.searchResults,
			);
			return this.searchResults;
		},
		showSearchResultsPane: function () {
			return !!(this.searchResults.length > 0 || this.panelVisibility);
		},
	},
	mounted() {
		this.currentPublication = this.submission.publications?.find(
			(p) => p.id === this.submission.currentPublicationId,
		);
		this.items =
			JSON.parse(JSON.stringify(this.currentPublication['igsn'])) ?? [];
	},
};
</script>

<style scoped>
#igsn {
	#pidManagerSearchResults {
		background-color: #fafafa;
		border: 1px solid #ccc;
		height: 150px;
		overflow-y: auto;
		position: absolute;
		width: calc(100% - 160px);
		box-shadow: 0 0.75rem 0.75rem #0003;
		margin-top: -30px;
		margin-left: 10px;

		td a:hover {
			background-color: #f1f1f1;
		}
	}

	.disabled {
		pointer-events: none;
		cursor: default;
		opacity: 0.3;
	}

	.cursor-pointer {
		cursor: pointer;
	}

	.center {
		text-align: center;
	}

	.inline-block {
		display: inline-block;
	}

	.p-0 {
		padding: 0;
	}

	.pt-60px {
		padding-top: 60px;
	}

	.h-40px {
		height: 40px;
	}

	.h-42px {
		height: 42px;
	}

	.w-40px {
		width: 40px;
	}

	.w-42px {
		width: 42px;
	}

	.w-5rem {
		width: 5rem;
	}

	.min-w-40px {
		min-width: 40px;
	}

	.line-height-40px {
		line-height: 40px;
	}

	.line-height-42px {
		line-height: 42px;
	}
}
</style>
