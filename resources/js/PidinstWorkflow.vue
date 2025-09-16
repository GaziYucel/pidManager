<template>
  <Workflow
      :publication=publication
      :pid-name=pidName
      :data-model=dataModel
      :api-url-data-cite=apiUrlDataCite
  ></Workflow>
</template>

<script setup>
import {computed, ref} from "vue";
import Workflow from './components/Workflow.vue';

const props = defineProps({
  publication: {type: Object, required: true},
});

const publication = computed(() => props.publication);

const pidName = ref('pidinst');
const dataModel = {doi: '', label: '', creators: '', publisher: '', publicationYear: ''};
const apiUrlDataCite = ref('https://api.datacite.org/dois?fields[dois]=titles,creators,publisher,publicationYear&query=types.resourceTypeGeneral:Instrument');
</script>
