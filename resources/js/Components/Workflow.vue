<template>
  <div>
    {{ t('plugins.generic.pidManager.' + pidName + '.generalDescription') }}<br/><br/>
    {{ t('plugins.generic.pidManager.' + pidName + '.workflow.instructions') }}
  </div>

  <!-- add from csv -->
  <div>
    <div>
      {{
        t('plugins.generic.pidManager.' + pidName + '.workflow.addFromCsv.instructions',
            {add: t('plugins.generic.pidManager.' + pidName + '.addFromCsv.button')})
      }}<br/><br/>
    </div>
    <div>
      <textarea class="pkpFormField__input pkpFormField--textarea__input !h-[9em]" v-model="csvString"></textarea>
    </div>
    <div>
      <PkpButton class="my-2" :is-required="true" @click="handleCsvString">
        {{ t('plugins.generic.pidManager.' + pidName + '.addFromCsv.button') }}
      </PkpButton>
      <span v-if="csvStringStatusMessage === 'success'" class="items-center py-[0.5rem] ml-1rem text-success">
        <Icon :icon="'Complete'" :class="'inline-block h-auto w-6 align-middle'" :inline="true"/>
        <span class="align-middle font-normal">
          {{ t('plugins.generic.pidManager.' + pidName + '.addFromCsv.success') }}
        </span>
      </span>
      <span v-if="csvStringStatusMessage === 'partial'" class="items-center py-[0.5rem] ml-1rem text-attention">
        <Icon :icon="'InProgress'" :class="'inline-block h-auto w-6 align-middle'" :inline="true"/>
        <span class="align-middle font-normal">
          {{ t('plugins.generic.pidManager.' + pidName + '.addFromCsv.partialSuccess') }}
        </span>
      </span>
      <span v-if="csvStringStatusMessage === 'empty'" class="items-center py-[0.5rem] ml-1rem">
        <Icon :icon="'Declined'" :class="'inline-block h-auto w-6 align-middle'" :inline="true"/>
        <span class="align-middle font-normal">
          {{ t('plugins.generic.pidManager.' + pidName + '.addFromCsv.inputEmpty') }}
        </span>
      </span>

    </div>
  </div>

  <!-- delete all items -->
  <div>
    <a class="cursor-pointer text-lg-normal" @click="deleteAllPids">
      {{ t('plugins.generic.pidManager.' + pidName + '.deleteAllLink') }}
    </a>
  </div>

  <!-- search -->
  <div :class="{disabled: !canEdit}">
    <table class="pkpTable w-full">
      <tr>
        <th>{{ t('plugins.generic.pidManager.' + pidName + '.workflow.table.pid') }}</th>
        <th>{{ t('plugins.generic.pidManager.' + pidName + '.workflow.table.title') }}</th>
        <th>&nbsp;</th>
      </tr>
      <tr>
        <td>
          <input v-model="searchPhraseDoi" type="text" class="pkpFormField__input w-full"
                 :placeholder="t('plugins.generic.pidManager.' + pidName + '.datacite.searchPhraseDoi.placeholder')"/>
        </td>
        <td>
          <input v-model="searchPhraseTitle" type="text" class="pkpFormField__input w-full"
                 :placeholder="t('plugins.generic.pidManager.' + pidName + '.datacite.searchPhraseTitle.placeholder')"/>
        </td>
        <td class="w-42px">
          <PkpButton v-if="!panelVisibility" @click="apiLookup" class="actionButton">
            <i class="fa fa-search" aria-hidden="true"></i>
          </PkpButton>
          <PkpButton v-if="panelVisibility" @click="clearSearch" class="actionButton">
            <i class="fa fa-times" aria-hidden="true"></i>
          </PkpButton>
        </td>
      </tr>
    </table>
    <div v-if="panelVisibility" class="searchResultsPane">
      <div v-if="panelVisibility === 'noResult'">
        <span class="center inline-block w-full pt-[60px]">
          {{ t('plugins.generic.pidManager.' + pidName + '.datacite.empty') }}
        </span>
      </div>
      <div v-else-if="panelVisibility === 'loading'">
        <span class="pkpSpinner center inline-block w-full pt-60px"></span>
      </div>
      <div v-else-if="panelVisibility === 'result'">
        <table class="pkpTable w-full">
          <template v-for="(row, j) in searchResults" :key="row.doi">
            <tr>
              <td class="p-0">
                <a :href="'https://doi.org/' + row.doi" class="block cursor-pointer" target="_blank">
                  <i class="fa fa-external-link"></i>
                </a>
              </td>
              <td class="p-0">
                <a @click="select(j)" :class="{disabled: row.exists}" class="block cursor-pointer">
                  <span>
                    <span v-if="row.creators">{{ row.creators }}</span>
                    <span v-if="row.publicationYear"> ({{ row.publicationYear }}).</span>
                    <span v-if="row.label"><em>{{ row.label }}</em>.</span>
                    <span v-if="row.publisher">{{ row.publisher }}.</span>
                    <span v-if="row.doi">{{ row.doi }}</span>
                  </span>
                </a>
              </td>
            </tr>
          </template>
        </table>
      </div>
    </div>
  </div>

  <!-- items -->
  <div>
    <PkpSearch
        :search-label="t('plugins.generic.pidManager.' + pidName + '.filter.placeholder')"
        @search-phrase-changed="(...args) => setItemsFilterPhrase(...args)"
    />
  </div>
  <table :class="{disabled: !canEdit}" class="pkpTable w-full">
    <tr>
      <th>{{ t('plugins.generic.pidManager.' + pidName + '.workflow.table.pid') }}</th>
      <th>{{ t('plugins.generic.pidManager.' + pidName + '.workflow.table.title') }}</th>
      <th>{{ t('plugins.generic.pidManager.' + pidName + '.workflow.table.creators') }}</th>
      <th>{{ t('plugins.generic.pidManager.' + pidName + '.workflow.table.publisher') }}</th>
      <th class="w-5rem">{{ t('plugins.generic.pidManager.' + pidName + '.workflow.table.publicationYear') }}</th>
      <th class="center w-42px">&nbsp;</th>
    </tr>
    <template v-for="(item, i) in itemsFiltered" :key="i">
      <tr>
        <td><input v-model="item.doi" type="text" class="pkpFormField__input w-full"/></td>
        <td><input v-model="item.label" type="text" class="pkpFormField__input w-full"/></td>
        <td><input v-model="item.creators" type="text" class="pkpFormField__input w-full"/></td>
        <td><input v-model="item.publisher" type="text" class="pkpFormField__input w-full"/></td>
        <td class="w-5rem"><input v-model="item.publicationYear" type="text" class="pkpFormField__input w-full"/></td>
        <td class="center w-42px">
          <PkpButton @click="remove(i)" class="actionButton">
            <i class="fa fa-trash" aria-hidden="true"></i>
          </PkpButton>
        </td>
      </tr>
    </template>
    <tr v-show="items.length === 0">
      <td colspan="6" class="center h-42px">
        {{ t('plugins.generic.pidManager.' + pidName + '.workflow.empty') }}
      </td>
    </tr>
  </table>
  <div :class="{disabled: !canEdit}">
    <PkpButton @click="add">
      {{ t('plugins.generic.pidManager.' + pidName + '.button.add') }}
    </PkpButton>
  </div>

  <!-- save -->
  <div :class="{disabled: !canEdit}" class="buttonRow pkpFormPage__footer footer">
    <span role="status" aria-live="polite" aria-atomic="true">
      <transition name="pkpFormPage__status">
          <span v-if="isSaving" class="pkpFormPage__status">
            <Spinner/>
            {{ t('common.saving') }}
          </span>
          <span v-else-if="hasRecentSave" class="pkpFormPage__status">
            <Icon icon="Complete" class="h-5 w-5 text-success" :inline="true"/>
            {{ t('form.saved') }}
          </span>
        </transition>
      </span>
    <PkpButton @click="save">
      {{ t('common.save') }}
    </PkpButton>
  </div>
</template>

<script setup>
import {ref, computed, onMounted} from 'vue';
import PkpButton from '@/components/Button/Button.vue';
import Icon from "@/components/Icon/Icon.vue";
import Spinner from "@/components/Spinner/Spinner.vue";
import PkpSearch from "@/components/Search/Search.vue";

const {useModal} = pkp.modules.useModal;
const {useLocalize} = pkp.modules.useLocalize;
const {useFetch} = pkp.modules.useFetch;
const {useDataChanged} = pkp.modules.useDataChanged;
const {t} = useLocalize();
const {openDialog} = useModal();
const {triggerDataChange} = useDataChanged();

const props = defineProps({
  publication: {type: Object, required: true},
  pidName: {type: String, required: true},
  dataModel: {type: Object, required: true},
  apiUrlDataCite: {type: String, required: true},
});
const {publication, pidName, dataModel, apiUrlDataCite} = props;
const items = ref([]);
const apiUrl = computed(() => pkp.context.apiBaseUrl + `submissions/pidManager/${publication.value.id}/${pidName}`);
const canEdit = computed(() => pkp.const.STATUS_PUBLISHED !== publication.value.status);

/* Add from csv */
const csvString = ref('');
const csvStringStatusMessage = ref('');
const handleCsvString = async () => {
  if (!csvString.value) {
    csvStringStatusMessage.value = 'empty';
    setTimeout(() => {
      csvStringStatusMessage.value = '';
    }, 5000);
    return;
  }

  const {fetch, data} = useFetch(apiUrl.value + '/parseCsv', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Csrf-Token': pkp.currentUser.csrfToken,
    },
    body: {
      csvString: csvString
    },
  });
  await fetch().then(() => {
    csvString.value = data.value.rejected;
    items.value = data.value.data;
    if (csvString.value) {
      csvStringStatusMessage.value = 'partial';
    } else {
      csvStringStatusMessage.value = 'success';
    }
    dataUpdateCallback();
  });
  setTimeout(() => {
    csvStringStatusMessage.value = '';
  }, 5000);
}

/* Delete all items */
function deleteAllPids() {
  openDialog({
    name: 'deleteAllPids',
    title: t('plugins.generic.pidManager.' + pidName + '.deleteAllDialog.title'),
    message: t('plugins.generic.pidManager.' + pidName + '.deleteAllDialog.description'),
    actions: [
      {
        label: t('common.delete'),
        isWarnable: true,
        callback: async (close) => {
          items.value = [];
          await save();
          close();
        },
      },
      {
        label: t('common.no'),
        isPrimary: true,
        callback: (close) => {
          close();
        },
      },
    ],
  });
}

/* Api lookup */
const searchPhraseDoi = ref('');
const searchPhraseTitle = ref('');
const rawSearchResults = ref([]);
const panelVisibility = ref(''); // '', 'noResult', 'loading', 'result'
const searchResults = computed(() => {
  return rawSearchResults.value.map(item => {
    return {
      ...dataModel,
      doi: item.id,
      label: item.attributes?.titles?.[0]?.title || '',
      publisher: item.attributes.publisher,
      publicationYear: item.attributes.publicationYear,
      creators: item.attributes.creators?.map(creator => {
        if (creator.nameType === 'Personal') {
          return `${creator.familyName}, ${creator.givenName?.[0]}.`;
        }
        return creator.name;
      }).join(', ') || '',
      exists: items.value.some(existingItem => existingItem.doi === item.id)
    };
  });
});
const apiLookup = async () => {
  const minLength = 3;
  let doi = searchPhraseDoi.value.trim().replace(/  +/g, ' ');
  let title = searchPhraseTitle.value.trim().replace(/  +/g, ' ');

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

  panelVisibility.value = 'loading';

  const {fetch, data} = useFetch(apiUrlDataCite + encodeURI(query), {});
  await fetch().then(() => {
    rawSearchResults.value = data.value.data;
    if (searchResults.value.length > 0) {
      panelVisibility.value = 'result';
    } else {
      panelVisibility.value = 'noResult';
    }
  });
};
const select = (index) => {
  const selectedDoi = searchResults.value[index].doi;
  if (items.value.some(item => item.doi === selectedDoi)) {
    return;
  }

  const newItem = Object.assign({}, dataModel, searchResults.value[index]);
  items.value.push(newItem);
};
const clearSearch = () => {
  rawSearchResults.value = [];
  panelVisibility.value = '';
}

/* Filtered items and filter phrase */
const itemsFilterPhrase = ref('');
const setItemsFilterPhrase = (value) => {
  itemsFilterPhrase.value = value;
}
const itemsFiltered = computed(() => {
  if (itemsFilterPhrase.value) {
    return filterArrayByPhrase(
        items.value,
        itemsFilterPhrase.value,
    );
  } else {
    return items.value;
  }
});
const containsItemsFilterPhrase = (obj, phrase) => {
  function deepSearch(value) {
    if (value === null || value === undefined) return false;

    if (typeof value === 'string') {
      return value.toLowerCase().includes(phrase.toLowerCase());
    }

    if (Array.isArray(value)) {
      return value.some(deepSearch);
    }

    if (typeof value === 'object') {
      return Object.values(value).some(deepSearch);
    }

    return false;
  }

  return deepSearch(obj);
}
const filterArrayByPhrase = (data, phrase) => {
  return data.filter((item) => containsItemsFilterPhrase(item, phrase));
}

/* Items */
const add = () => {
  items.value.push(JSON.parse(JSON.stringify(dataModel)));
}
const remove = (index) => {
  if (!items.value[index].doi && !items.value[index].label) {
    items.value.splice(index, 1);
    return;
  }
  openDialog({
    name: 'deletePid',
    title: t('plugins.generic.pidManager.' + pidName + '.remove.confirm'),
    message: '',
    actions: [
      {
        label: t('common.delete'),
        isWarnable: true,
        callback: async (close) => {
          items.value.splice(index, 1);
          close();
        },
      },
      {
        label: t('common.no'),
        isPrimary: true,
        callback: (close) => {
          close();
        },
      },
    ],
  });
}

/* Page */
const save = async () => {
  const {fetch} = useFetch(apiUrl.value, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Csrf-Token': pkp.currentUser.csrfToken,
    },
    body: JSON.stringify(
        items.value.filter((item) =>
            Object.values(item).some((value) => value !== null && value.length > 0)
        )
    ),
  });
  isSaving.value = true;
  await fetch().then(() => {
    setTimeout(() => {
      isSaving.value = false;
    }, 750);
    setTimeout(() => {
      hasRecentSave.value = true;
    }, 1000);
    setTimeout(() => {
      hasRecentSave.value = false;
    }, 4000);
  });
  dataUpdateCallback();
};

/* Helpers */
const isSaving = ref(false);
const hasRecentSave = ref(false);

function dataUpdateCallback() {
  triggerDataChange();
}

onMounted(() => {
  items.value = props.publication.value[pidName]
      ? JSON.parse(props.publication.value[pidName])
      : [];
});

/*
// This is needed for extracting localised texts by the plugin i18nExtractKeys
const localeKeys = [
  t("common.delete"),
  t("common.no"),
  t("common.save"),
  t('plugins.generic.pidManager.displayName'),
  t('plugins.generic.pidManager.description'),
  t('plugins.generic.pidManager.settings.title'),
  t('plugins.generic.pidManager.settings.description'),
  t('plugins.generic.pidManager.articleDetails.buttonShowAll.showAll'),
  t('plugins.generic.pidManager.articleDetails.buttonShowAll.minimise'),
  t('plugins.generic.pidManager.articleDetails.buttonShowAll.minimise'),
  t('plugins.generic.pidManager.igsn.settings.label'),
  t('plugins.generic.pidManager.igsn.label'),
  t('plugins.generic.pidManager.igsn.workflow.name'),
  t('plugins.generic.pidManager.igsn.workflow.label'),
  t('plugins.generic.pidManager.igsn.workflow.instructions'),
  t('plugins.generic.pidManager.igsn.workflow.addFromCsv.instructions'),
  t('plugins.generic.pidManager.igsn.addFromCsv.button'),
  t('plugins.generic.pidManager.igsn.generalDescription'),
  t('plugins.generic.pidManager.igsn.submission.instructions'),
  t('plugins.generic.pidManager.igsn.addFromCsv.success'),
  t('plugins.generic.pidManager.igsn.addFromCsv.partialSuccess'),
  t('plugins.generic.pidManager.igsn.addFromCsv.inputEmpty'),
  t('plugins.generic.pidManager.igsn.deleteAllLink'),
  t('plugins.generic.pidManager.igsn.deleteAllDialog.title'),
  t('plugins.generic.pidManager.igsn.deleteAllDialog.description'),
  t('plugins.generic.pidManager.igsn.filter.placeholder'),
  t('plugins.generic.pidManager.igsn.workflow.empty'),
  t('plugins.generic.pidManager.igsn.workflow.table.pid'),
  t('plugins.generic.pidManager.igsn.workflow.table.title'),
  t('plugins.generic.pidManager.igsn.workflow.table.creators'),
  t('plugins.generic.pidManager.igsn.workflow.table.publisher'),
  t('plugins.generic.pidManager.igsn.workflow.table.publicationYear'),
  t('plugins.generic.pidManager.igsn.button.add'),
  t('plugins.generic.pidManager.igsn.remove.confirm'),
  t('plugins.generic.pidManager.igsn.datacite.searchPhraseDoi.placeholder'),
  t('plugins.generic.pidManager.igsn.datacite.searchPhraseTitle.placeholder'),
  t('plugins.generic.pidManager.igsn.articleDetails.details'),
  t('plugins.generic.pidManager.igsn.datacite.info'),
  t('plugins.generic.pidManager.igsn.datacite.empty'),
  t('plugins.generic.pidManager.pidinst.settings.label'),
  t('plugins.generic.pidManager.pidinst.label'),
  t('plugins.generic.pidManager.pidinst.workflow.name'),
  t('plugins.generic.pidManager.pidinst.workflow.label'),
  t('plugins.generic.pidManager.pidinst.workflow.instructions'),
  t('plugins.generic.pidManager.pidinst.generalDescription'),
  t('plugins.generic.pidManager.pidinst.submission.instructions'),
  t('plugins.generic.pidManager.pidinst.workflow.addFromCsv.instructions'),
  t('plugins.generic.pidManager.pidinst.addFromCsv.button'),
  t('plugins.generic.pidManager.pidinst.addFromCsv.success'),
  t('plugins.generic.pidManager.pidinst.addFromCsv.partialSuccess'),
  t('plugins.generic.pidManager.pidinst.addFromCsv.inputEmpty'),
  t('plugins.generic.pidManager.pidinst.deleteAllLink'),
  t('plugins.generic.pidManager.pidinst.deleteAllDialog.title'),
  t('plugins.generic.pidManager.pidinst.deleteAllDialog.description'),
  t('plugins.generic.pidManager.pidinst.filter.placeholder'),
  t('plugins.generic.pidManager.pidinst.workflow.empty'),
  t('plugins.generic.pidManager.pidinst.workflow.table.pid'),
  t('plugins.generic.pidManager.pidinst.workflow.table.title'),
  t('plugins.generic.pidManager.pidinst.workflow.table.creators'),
  t('plugins.generic.pidManager.pidinst.workflow.table.publisher'),
  t('plugins.generic.pidManager.pidinst.workflow.table.publicationYear'),
  t('plugins.generic.pidManager.pidinst.button.add'),
  t('plugins.generic.pidManager.pidinst.remove.confirm'),
  t('plugins.generic.pidManager.pidinst.datacite.searchPhraseDoi.placeholder'),
  t('plugins.generic.pidManager.pidinst.datacite.searchPhraseTitle.placeholder'),
  t('plugins.generic.pidManager.pidinst.articleDetails.details'),
  t('plugins.generic.pidManager.pidinst.datacite.info'),
  t('plugins.generic.pidManager.pidinst.datacite.empty')
];
*/
</script>

<style scoped>
.searchResultsPane {
  background-color: #fafafa;
  border: 1px solid #ccc;
  height: 150px;
  overflow-y: auto;
  position: absolute;
  width: calc(100% - 28rem);
  box-shadow: 0 .75rem .75rem #0003;
  margin-top: -16px;
  margin-left: 16px;
  z-index: 999;

  td {
    padding: 0;
  }

  td a:hover {
    background-color: #f1f1f1;
  }

  td:first-child {
    width: 3rem;
  }

  td:first-child a {
    min-height: 3rem;
    line-height: 3rem;
    width: 3rem;
    text-align: center;
  }

  td:last-child a {
    min-height: 3rem;
    line-height: 1.5rem;
    display: flex;
    align-items: center;
    padding-left: 0.25rem;
  }
}

.actionButton {
  width: 2.5rem;
  height: 2.5rem;
}

.footer {
  margin-left: -1.25rem;
  margin-right: -1.25rem;
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

.h-42px {
  height: 42px;
}

.w-42px {
  width: 42px;
}

.w-5rem {
  width: 5rem;
}

.ml-1rem {
  margin-left: 1rem;
}
</style>
