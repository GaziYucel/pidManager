{**
 * templates/igsnPublicationTab.tpl
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * IGSN tab
 *
 * https://schema.datacite.org/meta/kernel-4.5/
 * https://support.datacite.org/docs/api-queries#selecting-which-metadata-fields-to-retrieve
 *}

{assign var="ConstantsIgsn" value=APP\plugins\generic\pidManager\classes\Constants::igsn}
{assign var="SubmissionWizardOpen" value="<div id='pidManagerIgsn' v-if='section.id === \"titleAbstract\"'>"}
{assign var="SubmissionWizardClose" value="</div>"}
{assign var="PublicationTabOpen" value="<tab id='pidManagerIgsn' class='pkpTab' role='tabpanel' label='"|cat:__("plugins.generic.pidManager.igsn.workflow.name")|cat:"'>"}
{assign var="PublicationTabClose" value="</tab>"}

{if $location==="SubmissionWizard"}{$SubmissionWizardOpen}{/if}
{if $location==="PublicationTab"}{$PublicationTabOpen}{/if}

<link rel="stylesheet" href="{$assetsUrl}/css/backend.css" type="text/css"/>

<div class="header">
  <h4>{translate key="plugins.generic.pidManager.igsn.workflow.label"}</h4>
  <span>{translate key="plugins.generic.pidManager.igsn.workflow.description"}</span>
</div>

<div class="content" id="pidManager-igsn-workflow-content">
  <table>
    <tr>
      <th class="column1">
        <span>{translate key="plugins.generic.pidManager.igsn.workflow.table.pid"}</span>
      </th>
      <th class="column2">
        <span>{translate key="plugins.generic.pidManager.igsn.workflow.table.label"}</span>
      </th>
      <th class="column3">
        <span> &nbsp; </span>
      </th>
    </tr>
    <tr>
      <td class="column1">
        <input v-model="pidManagerIgsnApp.searchPhraseDoi" type="text"
               class="pkpFormField__input pkpFormField--text__input"/>
      </td>
      <td class="column2">
        <input v-model="pidManagerIgsnApp.searchPhraseLabel" type="text"
               class="pkpFormField__input pkpFormField--text__input"/>
      </td class="column3">
      <td class="column3">
        <a @click="pidManagerIgsnApp.apiLookup()" class="pkpButton"
           :class="{ 'pidManager-Disabled': pidManagerIgsnApp.isPublished }">
          <i class="fa fa-search" aria-hidden="true"></i>
        </a>
      </td>
    </tr>
    <tr>
      <td class="column1" colspan="2">
        <div id="pidManagerSearchResults">
          <span v-if="pidManagerIgsnApp.panelVisibility.empty">
								{translate key="plugins.generic.pidManager.igsn.datacite.empty"}
          </span>
          <span v-else-if="pidManagerIgsnApp.panelVisibility.spinner" aria-hidden="true"
                class="pkpSpinner">
          </span>
          <table v-else-if="pidManagerIgsnApp.panelVisibility.list">
            <tr v-for="(row, j) in pidManagerIgsnApp.searchResultsFiltered">
              <td class="column1">
                <a :href="'https://doi.org/' + row.doi" target="_blank">
                  <i class="fa fa-external-link"></i>
                </a>
              </td>
              <td class="column2">
                <a @click.prevent="pidManagerIgsnApp.select(j)"
                   :class="{ 'pidManager-Disabled': row.exists }">
                  {{ row.label }} [{{ row.doi }}]
                </a>
              </td>
            </tr>
          </table>
          <span v-else>
            {translate key="plugins.generic.pidManager.igsn.datacite.info"}
          </span>
        </div>
      </td>
      <td class="column3">
        <a @click="pidManagerIgsnApp.clearSearch()" class="pkpButton">
          <icon icon="times"></icon>
        </a>
      </td>
    </tr>
    <tr>
      <td colspan="3"> &nbsp;</td>
    </tr>
    <tr>
      <th class="column1">
        <span>{translate key="plugins.generic.pidManager.igsn.workflow.table.pid"}</span>
      </th>
      <th class="column2">
        <span>{translate key="plugins.generic.pidManager.igsn.workflow.table.label"}</span>
      </th>
      <th class="column3">
        <span> &nbsp; </span>
      </th>
    </tr>
    <tbody>
    <template v-for="(igsn, i) in pidManagerIgsnApp.igsns" class="pidManager-Row">
      <tr>
        <td class="column1">
          <input v-model="igsn.doi" type="text" class="pkpFormField__input pkpFormField--text__input"/>
        </td>
        <td class="column2">
          <input v-model="igsn.label" type="text" class="pkpFormField__input pkpFormField--text__input"/>
        </td>
        <td class="column3">
          <a @click="pidManagerIgsnApp.remove(i)" class="pkpButton"
             :class="{ 'pidManager-Disabled': pidManagerIgsnApp.isPublished }">
            <i class="fa fa-trash" aria-hidden="true"></i>
          </a>
        </td>
      </tr>
    </template>
    <tr v-show="pidManagerIgsnApp.igsns.length === 0">
      <td colspan="3">
        <p>
          {translate key="plugins.generic.pidManager.igsn.workflow.empty"}
        </p>
      </td>
    </tr>
    <tr>
      <td colspan="3">
        <p>
          <a class="pkpButton" v-on:click="pidManagerIgsnApp.add()"
             v-show="!pidManagerIgsnApp.isPublished">
            {translate key="plugins.generic.pidManager.igsn.button.add"}
          </a>
        </p>
      </td>
    </tr>
    </tbody>
  </table>
</div>

{if $location==="PublicationTab"}
  <div class="footer" id="pidManager-igsn-workflow-footer">
    <pkp-form v-bind="components.{$ConstantsIgsn}" @set="set"></pkp-form>
    <span class="pidManager-Hide">
      {{ pidManagerIgsnApp.workingPublication = workingPublication }}
      {{ pidManagerIgsnApp.configure() }}
      {{ components.{$ConstantsIgsn}.fields[0]['value'] = JSON.stringify(pidManagerIgsnApp.igsnListClean) }}
      {{ components.{$ConstantsIgsn}.action = '{$apiBaseUrl}submissions/' + workingPublication.submissionId + '/publications/' + workingPublication.id }}
    </span>
  </div>
{/if}

<script>
  let pidManagerIgsnApp = new pkp.Vue({
    data() {
      return {
        igsns: {$igsns},
        searchPhraseDoi: '',
        searchPhraseLabel: '',
        searchResults: [], // [ { 'id': '', 'label': '' }, ... ]
        panelVisibility: { /**/ empty: false, spinner: false, list: false},
        panelVisibilityDefault: { /**/ empty: false, spinner: false, list: false},
        igsnModel: { /**/ 'doi': '', 'label': ''},
        minimumSearchPhraseLength: 3,
        pendingRequests: new WeakMap(),
        publication: { /**/ id: 0},
        workingPublication: { /**/ id: 0}, // workingPublication
        apiBaseQuery: 'https://api.datacite.org/dois?fields[dois]=titles&query=relatedIdentifiers.relatedIdentifierType:IGSN AND types.resourceTypeGeneral:PhysicalObject'
      };
    },
    computed: {
      igsnListClean: function () {
        let result = JSON.parse(JSON.stringify(this.igsns));
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
          for (let i = 0; i < this.igsns.length; i++) {
            if (this.igsns[i].doi === item.doi) {
              item.exists = true;
            }
          }
        });
        return this.searchResults;
      }
    },
    methods: {
      configure: function () {
        if (document.querySelector('#pidManagerIgsn button.pkpButton') !== null) {
          let saveBtn = document.querySelector('#pidManagerIgsn button.pkpButton');
          saveBtn.disabled = this.isPublished;
        }
      },
      add: function () {
        this.igsns.push(JSON.parse(JSON.stringify(this.igsnModel)));
      },
      remove: function (index) {
        if (!this.igsns[index].doi && !this.igsns[index].label) {
          this.igsns.splice(index, 1);
          return;
        }
        if (confirm('{translate key="plugins.generic.pidManager.igsn.button.remove.confirm"}') === true) {
          this.igsns.splice(index, 1);
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
      getQueryPart: function (query) {
        query = query.replace(/[.,\/#!$%^&*;:{ }=\-_`~()—+]/g, ' ');
        query = query.replace(/\s\s+/g, ' ');
        query = query.trim();
        query = query.replaceAll(' ', '*+*');
        query = '*' + query + '*';
        return query;
      },
      apiLookup: function () {
        if (this.searchPhraseDoi.length < this.minimumSearchPhraseLength &&
          this.searchPhraseLabel.length < this.minimumSearchPhraseLength
        ) {
          return;
        }
        let query = '';
        if (this.searchPhraseDoi.length >= this.minimumSearchPhraseLength) {
          query += ' AND id:' + this.getQueryPart(this.searchPhraseDoi);
        }
        if (this.searchPhraseLabel.length >= this.minimumSearchPhraseLength) {
          query += ' AND titles.title:' + this.getQueryPart(this.searchPhraseLabel);
        }
        if (query.length === 0) return;

        const url = this.apiBaseQuery + query + '';

        this.panelVisibilityShowPart('spinner');
        const controller = new AbortController();
        this.pendingRequests.set(this, controller);

        fetch(url, {
          signal: controller.signal
        })
          .then(response => response.json())
          .then(responseData => {
            console.log(responseData);
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
          let label = '';
          let exists = false;

          for (let i = 0; i < item.attributes.titles.length; i++) {
            label = item.attributes.titles[i].title;
          }

          for (let i = 0; i < this.igsns.length; i++) {
            if (this.igsns[i].doi === item.id) exists = true;
          }

          searchResults.push({ /**/ doi: item.id, label: label, exists: exists});
        });
        this.searchResults = searchResults;
      },
      select: function (index) {
        let newIgsn = {
          doi: this.searchResults[index].doi,
          label: this.searchResults[index].label,
        };
        this.igsns.push(newIgsn);
      },
    },
    watch: {
      workingPublication(newValue, oldValue) {
        if (newValue !== oldValue) {
          this.publication = this.workingPublication;
          console.log('pidManagerIgsn:workingPublication: ' + oldValue['id'] + ' > ' + newValue['id']);
        }
      }
    }
  });
</script>

{if $location==="PublicationTab"}{$PublicationTabClose}{/if}
{if $location==="SubmissionWizard"}{$SubmissionWizardClose}{/if}
