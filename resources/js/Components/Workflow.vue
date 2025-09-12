<template>
  <div>
    {{ t('plugins.generic.pidManager.' + pidName + '.generalDescription') }}<br/><br/>
    {{ t('plugins.generic.pidManager.' + pidName + '.workflow.instructions') }}
  </div>

  <!-- search -->
  <div :class="{disabled: isPublished}">
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
  <table :class="{disabled: isPublished}" class="pkpTable w-full">
    <tr>
      <th>{{ t('plugins.generic.pidManager.' + pidName + '.workflow.table.pid') }}</th>
      <th>{{ t('plugins.generic.pidManager.' + pidName + '.workflow.table.title') }}</th>
      <th>{{ t('plugins.generic.pidManager.' + pidName + '.workflow.table.creators') }}</th>
      <th>{{ t('plugins.generic.pidManager.' + pidName + '.workflow.table.publisher') }}</th>
      <th>{{ t('plugins.generic.pidManager.' + pidName + '.workflow.table.publicationYear') }}</th>
      <th class="center w-42px">&nbsp;</th>
    </tr>
    <template v-for="(item, i) in items" :key="i">
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
      <td colspan="6" class="center h-42px w-42px">
        {{ t('plugins.generic.pidManager.' + pidName + '.workflow.empty') }}
      </td>
    </tr>
  </table>
  <div :class="{disabled: isPublished}">
    <PkpButton @click="add">
      {{ t('plugins.generic.pidManager.' + pidName + '.button.add') }}
    </PkpButton>
  </div>

  <div :class="{disabled: isPublished}" class="buttonRow pkpFormPage__footer footer">
    <span role="status"></span>
    <PkpButton @click="save">
      {{ t('common.save') }}
    </PkpButton>
  </div>
</template>

<script setup>
import {ref, computed, onMounted} from 'vue';
import PkpButton from '@/components/Button/Button.vue';

const {useModal} = pkp.modules.useModal;
const {useLocalize} = pkp.modules.useLocalize;
const {useFetch} = pkp.modules.useFetch;
const {t} = useLocalize();
const {openDialog} = useModal();

const props = defineProps({
  submission: {type: Object, required: true},
  pidName: {type: String, required: true},
  apiUrlDataCite: {type: String, required: true},
});
const {pidName, apiUrlDataCite} = props;

const dataModel = {doi: '', label: '', creators: '', publisher: '', publicationYear: ''};
const items = ref([]);
const currentPublication = ref({});
const apiUrl = ref('');
const isPublished = computed(() => pkp.const.STATUS_PUBLISHED === currentPublication.value.status);

/* Api lookup */
const searchPhraseDoi = ref('');
const searchPhraseTitle = ref('');
const rawSearchResults = ref([]);
const panelVisibility = ref(''); // '', 'noResult', 'loading', 'result'
const searchResults = computed(() => {
  let results = [];

  rawSearchResults.value.forEach((item) => {
    let itemChanged = JSON.parse(JSON.stringify(dataModel));

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
    for (let i = 0; i < items.value.length; i++) {
      if (items.value[i].doi === item.id) {
        itemChanged['exists'] = true;
      }
    }

    results.push(itemChanged);
  });

  return results;
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
  for (let i = 0; i < items.value.length; i++) {
    if (items.value[i].doi === searchResults.value[index].doi) {
      return;
    }
  }

  let newItem = JSON.parse(JSON.stringify(dataModel));
  Object.keys(newItem).forEach((key) => {
    newItem[key] = searchResults.value[index][key];
  });
  items.value.push(newItem);
};
const clearSearch = () => {
  rawSearchResults.value = [];
  panelVisibility.value = '';
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
  await fetch();
};

onMounted(() => {
  currentPublication.value = props.submission.publications?.find(
      (item) => item.id === props.submission.currentPublicationId
  );

  items.value = currentPublication.value[pidName]
      ? JSON.parse(currentPublication.value[pidName])
      : [];

  apiUrl.value = pkp.context.apiBaseUrl +
      `submissions/pidManager/${currentPublication.value.id}/${pidName}`;
});

/*
// This is needed for extracting localised texts by the plugin i18nExtractKeys
const localeKeys = [
  t("common.delete"),
  t("common.no"),
  t("common.save"),
  t("plugins.generic.pidManager.igsn.button.add"),
  t("plugins.generic.pidManager.igsn.datacite.empty"),
  t("plugins.generic.pidManager.igsn.datacite.searchPhraseDoi.placeholder"),
  t("plugins.generic.pidManager.igsn.datacite.searchPhraseTitle.placeholder"),
  t("plugins.generic.pidManager.igsn.generalDescription"),
  t("plugins.generic.pidManager.igsn.remove.confirm"),
  t("plugins.generic.pidManager.igsn.workflow.empty"),
  t("plugins.generic.pidManager.igsn.workflow.instructions"),
  t("plugins.generic.pidManager.igsn.workflow.table.creators"),
  t("plugins.generic.pidManager.igsn.workflow.table.pid"),
  t("plugins.generic.pidManager.igsn.workflow.table.publicationYear"),
  t("plugins.generic.pidManager.igsn.workflow.table.publisher"),
  t("plugins.generic.pidManager.igsn.workflow.table.title"),
  t("plugins.generic.pidManager.pidinst.button.add"),
  t("plugins.generic.pidManager.pidinst.datacite.empty"),
  t("plugins.generic.pidManager.pidinst.datacite.searchPhraseDoi.placeholder"),
  t("plugins.generic.pidManager.pidinst.datacite.searchPhraseTitle.placeholder"),
  t("plugins.generic.pidManager.pidinst.generalDescription"),
  t("plugins.generic.pidManager.pidinst.remove.confirm"),
  t("plugins.generic.pidManager.pidinst.workflow.empty"),
  t("plugins.generic.pidManager.pidinst.workflow.instructions"),
  t("plugins.generic.pidManager.pidinst.workflow.table.creators"),
  t("plugins.generic.pidManager.pidinst.workflow.table.pid"),
  t("plugins.generic.pidManager.pidinst.workflow.table.publicationYear"),
  t("plugins.generic.pidManager.pidinst.workflow.table.publisher"),
  t("plugins.generic.pidManager.pidinst.workflow.table.title")
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
</style>
