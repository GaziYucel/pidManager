<template>
	<div id="igsn" role="tabpanel" class="pkpTab">
		<div>{{ t('plugins.generic.pidManager.igsn.workflow.name') }}</div>

		<div class="header">
			<h4 class="mt-0">
				{{ t('plugins.generic.pidManager.igsn.workflow.label') }}
			</h4>
			<span>
				{{ t('plugins.generic.pidManager.igsn.workflow.description') }}
			</span>
		</div>

		<div class="content">
			<!-- 🔍 Search -->
			<table class="w-full pt-16">
				<tr>
					<th>
						<span class="block">
							{{ t('plugins.generic.pidManager.igsn.workflow.table.pid') }}
						</span>
					</th>
					<th>
						<span class="block">
							{{ t('plugins.generic.pidManager.igsn.workflow.table.label') }}
						</span>
					</th>
					<th class="center w-42">&nbsp;</th>
				</tr>
				<tr>
					<td>
						<input
							v-model="searchPhraseDoi"
							type="text"
							class="pkpFormField__input pkpFormField--text__input"
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
							class="pkpFormField__input pkpFormField--text__input"
							:placeholder="
								t(
									'plugins.generic.pidManager.igsn.datacite.searchPhraseLabel.placeholder',
								)
							"
						/>
					</td>
					<td class="center w-42">
						<a
							@click="apiLookup"
							class="pkpButton line-height-40 h-40 min-w-40"
							:class="{disabled: isPublished}"
						>
							<i class="fa fa-search" aria-hidden="true"></i>
						</a>
					</td>
				</tr>

				<!-- Results Pane -->
				<tr v-if="showSearchResultsPane">
					<td colspan="2">
						<div id="pidManagerSearchResults">
							<span
								v-if="panelVisibility.empty"
								class="center inline-block w-full pt-60"
							>
								{{ t('plugins.generic.pidManager.igsn.datacite.empty') }}
							</span>
							<span
								v-else-if="panelVisibility.spinner"
								class="pkpSpinner center inline-block w-full pt-60"
							></span>
							<table v-else-if="panelVisibility.list" class="w-full">
								<template
									v-for="(row, j) in searchResultsFiltered"
									:key="row.doi"
								>
									<tr>
										<td class="center w-42 p-0">
											<a :href="'https://doi.org/' + row.doi" target="_blank">
												<i class="fa fa-external-link"></i>
											</a>
										</td>
										<td class="p-0">
											<a
												@click="select(j)"
												class="searchRowLink"
												:class="{disabled: row.exists}"
											>
												{{ row.label }} [{{ row.doi }}]
											</a>
										</td>
									</tr>
								</template>
							</table>
						</div>
					</td>
					<td class="center w-42">
						<a
							@click="clearSearch"
							class="pkpButton line-height-40 h-40 min-w-40"
						>
							<i aria-hidden="true" class="fa fa-times"></i>
						</a>
					</td>
				</tr>

				<tr v-else>
					<td colspan="3" class="h-42">&nbsp;</td>
				</tr>
			</table>

			<!-- 📋 Items -->
			<table class="w-full">
				<tr>
					<th>
						<span class="block">
							{{ t('plugins.generic.pidManager.igsn.workflow.table.pid') }}
						</span>
					</th>
					<th>
						<span class="block">
							{{ t('plugins.generic.pidManager.igsn.workflow.table.label') }}
						</span>
					</th>
					<th>
						<span class="block">
							{{ t('plugins.generic.pidManager.igsn.workflow.table.creators') }}
						</span>
					</th>
					<th>
						<span class="block">
							{{
								t('plugins.generic.pidManager.igsn.workflow.table.publisher')
							}}
						</span>
					</th>
					<th class="w-4rem">
						<span class="block">
							{{
								t(
									'plugins.generic.pidManager.igsn.workflow.table.publicationYear',
								)
							}}
						</span>
					</th>
					<th class="center w-42">&nbsp;</th>
				</tr>

				<template v-for="(item, i) in items" :key="i">
					<tr>
						<td>
							<input
								v-model="item.doi"
								type="text"
								class="pkpFormField__input pkpFormField--text__input"
							/>
						</td>
						<td>
							<input
								v-model="item.label"
								type="text"
								class="pkpFormField__input pkpFormField--text__input"
							/>
						</td>
						<td>
							<input
								v-model="item.creators"
								type="text"
								class="pkpFormField__input pkpFormField--text__input"
							/>
						</td>
						<td>
							<input
								v-model="item.publisher"
								type="text"
								class="pkpFormField__input pkpFormField--text__input"
							/>
						</td>
						<td>
							<input
								v-model="item.publicationYear"
								type="text"
								class="pkpFormField__input pkpFormField--text__input"
							/>
						</td>
						<td class="center w-42">
							<a
								@click="remove(i)"
								class="pkpButton line-height-40 h-40 min-w-40"
								:class="{disabled: isPublished}"
							>
								<i class="fa fa-trash" aria-hidden="true"></i>
							</a>
						</td>
					</tr>
				</template>

				<tr v-show="items.length === 0">
					<td colspan="6" class="center w-42 h-42">
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

		<!-- 🔧 Form -->
		<div class="footer">
			<!--			<pkp-form v-bind="components.igsn" @set="set"></pkp-form>-->
		</div>
	</div>
</template>

<script setup>
import {ref, computed, watch, onMounted, reactive} from 'vue';

const {useFetch} = pkp.modules.useFetch;
const {useLocalize} = pkp.modules.useLocalize;
const {t} = useLocalize();

const props = defineProps({
	submission: {type: Object, required: true},
});

const {submission} = props;
const publication = submission.publications?.find(
	(p) => p.id === submission.currentPublicationId,
);

// console.log('props', props);
console.log('submission', submission);
console.log('publication', publication);
console.log('props', props);
console.log('pkp', pkp);

// const {triggerDataChange} = useDataChanged();
// function dataUpdateCallback() {
// 	triggerDataChange();
// }

// ⚙️ State
const items = ref([]);
const dataModel = {
	doi: '',
	label: '',
	creators: '',
	publisher: '',
	publicationYear: '',
};
const workingPublication = ref({});
const apiUrl = ref(
	'https://api.datacite.org/dois?fields[dois]=titles,creators,publisher,publicationYear&query=relatedIdentifiers.relatedIdentifierType:IGSN AND types.resourceTypeGeneral:PhysicalObject',
);

const searchPhraseDoi = ref('');
const searchPhraseLabel = ref('');
const searchResults = ref([]);
const panelVisibilityDefault = {empty: false, spinner: false, list: false};
const panelVisibility = reactive({empty: false, spinner: false, list: false});
const minimumSearchPhraseLength = 3;
const pendingRequests = new WeakMap();

const isPublished = computed(() => {
	return false;
	// return pkp.const.STATUS_PUBLISHED === workingPublication.value.status;
});

const searchResultsFiltered = computed(() => {
	searchResults.value.forEach((item) => {
		item.exists = items.value.some((i) => i.doi === item.doi);
	});
	return searchResults.value;
});

const showSearchResultsPane = computed(() => {
	return (
		searchResults.value.length > 0 ||
		panelVisibility.empty ||
		panelVisibility.spinner ||
		panelVisibility.list
	);
});

// function configure() {
// 	const saveBtn = document.querySelector(
// 		'#pidManager-workflow button.pkpButton',
// 	);
// 	if (saveBtn) {
// 		saveBtn.disabled = isPublished.value;
// 	}
// }

function add() {
	items.value.push(JSON.parse(JSON.stringify(dataModel)));
}

function remove(index) {
	if (!items.value[index].doi && !items.value[index].label) {
		items.value.splice(index, 1);
		return;
	}
	if (confirm(t('plugins.generic.pidManager.igsn.remove.confirm'))) {
		items.value.splice(index, 1);
	}
}

function clearSearch() {
	searchPhraseDoi.value = '';
	searchPhraseLabel.value = '';
	searchResults.value = [];
	panelVisibilityReset();
	stopPendingRequests();
}

function stopPendingRequests() {
	const previousController = pendingRequests.get(this);
	if (previousController) previousController.abort();
}

function panelVisibilityShowPart(part) {
	Object.assign(panelVisibility, {...panelVisibilityDefault});
	panelVisibility[part] = true;
}

function panelVisibilityReset() {
	Object.assign(panelVisibility, {...panelVisibilityDefault});
}

function getDoiCleaned(doi) {
	return `*${doi.replace(/  +/g, ' ').trim().replaceAll(' ', '*+*')}*`;
}

function getLabelCleaned(label) {
	return getDoiCleaned(label.replace(/[.,\/#!$%^&*;:{ }=\-_`~()—+]/g, ' '));
}

// const fetchResults = ref({});

async function apiLookup() {
	if (
		searchPhraseDoi.value.length < minimumSearchPhraseLength &&
		searchPhraseLabel.value.length < minimumSearchPhraseLength
	) {
		return;
	}

	let query = '';
	if (searchPhraseDoi.value.length >= minimumSearchPhraseLength) {
		query += ' AND id:' + getDoiCleaned(searchPhraseDoi.value);
	}
	if (searchPhraseLabel.value.length >= minimumSearchPhraseLength) {
		query += ' AND titles.title:' + getLabelCleaned(searchPhraseLabel.value);
	}
	if (query.length === 0) {
		return;
	}

	const url = apiUrl.value + query;
	panelVisibilityShowPart('spinner');
	const controller = new AbortController();
	pendingRequests.set(searchResults, controller);

	// setSearchResults(data);
	// panelVisibilityShowPart('list');
	// if (searchResults.value.length === 0) {
	// 	panelVisibilityShowPart('empty');
	// }

	// const {fetch, data} = useFetch(url, {});
	// await fetch();
	// await panelVisibilityShowPart('list');
	// await setSearchResults(data);
	// console.log('data', data);

	fetch(url, { signal: controller.signal })
	// fetch(url, {})
		.then((r) => r.json())
		.then((responseData) => {
			setSearchResults(responseData.data);
			console.log('responseData.data', responseData.data);
			console.log('searchResults', searchResults);
			panelVisibilityShowPart('list');
			if (searchResults.value.length === 0) panelVisibilityShowPart('empty');
		});
}

function setSearchResults(itemsFetched) {
	const results = [];
	itemsFetched.forEach((item) => {
		const itemChanged = JSON.parse(JSON.stringify(dataModel));
		itemChanged.doi = item.id;

		if (item.attributes?.titles?.length > 0) {
			itemChanged.label = item.attributes.titles[0].title;
		}

		if (item.attributes?.creators?.length > 0) {
			itemChanged.creators = '';
			item.attributes.creators.forEach((c) => {
				if (itemChanged.creators) itemChanged.creators += ', ';
				if (c.nameType === 'Personal') {
					itemChanged.creators += `${c.familyName}, ${c.givenName?.substring(0, 1)}.`;
				} else {
					itemChanged.creators += c.name;
				}
			});
		}

		itemChanged.publisher = item.attributes.publisher;
		itemChanged.publicationYear = item.attributes.publicationYear;
		// itemChanged.exists = items.value.find((i) => i.doi === item.id);
		results.push(itemChanged);
	});
	searchResults.value = results;
}

function select(index) {
	const newItem = JSON.parse(JSON.stringify(dataModel));
	Object.keys(newItem).forEach((key) => {
		newItem[key] = searchResults.value[index][key];
	});
	items.value.push(newItem);
}

watch(workingPublication, (newValue, oldValue) => {
	if (newValue !== oldValue) {
		publication.value = workingPublication.value;
	}
});

// 🔗 Sync PKP components automatically
// watchEffect(() => {
// 	if (components?.igsn) {
// 		components.igsn.fields[0].value = JSON.stringify(itemListCleaned.value);
// 		components.igsn.action =
// 			"{\$apiBaseUrl}submissions/" +
// 			workingPublication.value.submissionId +
// 			"/publications/" +
// 			workingPublication.value.id;
// 	}
// });

onMounted(() => {
	// configure();
	items.value = JSON.parse(JSON.stringify(publication['igsn'])) ?? [];
});
</script>

<style scoped>
#igsn {
	padding-top: 32px;
	padding-bottom: 0;
	margin-bottom: 32px;
	border-left: 1px solid #ddd;

	table th span {
		background-color: #f9f9f9;
		border-bottom: 1px solid #ddd;
	}

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

		td a {
			display: block;
			cursor: pointer;
			padding: 0.3rem;
			color: inherit;
		}

		td a:hover {
			background-color: #f1f1f1;
		}
	}

	/* class focussed */

	.disabled {
		pointer-events: none;
		cursor: default;
		opacity: 0.3;
	}

	.hide {
		display: none !important;
	}

	.center {
		text-align: center;
	}

	.h-40 {
		height: 40px;
	}

	.h-42 {
		height: 42px;
	}

	.h-51 {
		height: 51px;
	}

	.min-w-40 {
		min-width: 40px;
	}

	.line-height-40 {
		line-height: 40px;
	}

	.w-4rem {
		width: 4rem;
	}

	.w-42 {
		width: 42px;
	}

	.w-full {
		width: 100%;
	}

	.p-0 {
		padding: 0;
	}

	.p-5 {
		padding: 5px;
	}

	.pb-0 {
		padding-bottom: 0;
	}

	.pt-16 {
		padding-top: 16px;
	}

	.pt-32 {
		padding-top: 32px;
	}

	.pt-60 {
		padding-top: 60px;
	}

	.mb-32 {
		margin-bottom: 32px;
	}

	.mt-0 {
		margin-top: 0;
	}

	.block {
		display: block;
	}

	.inline-block {
		display: inline-block;
	}

	/* OJS elements */

	.pkpFormField--text__input {
		width: 100%;
	}

	.pkpFormPages .pkpFormPage .pkpFormGroup {
		display: none;
		padding: 0.35em 0.75em 0.625em;
	}
}

/* OJS elements */
.pkpPublication__tabs > .pkpTabs__buttons {
	border-right: 0;
}
</style>
