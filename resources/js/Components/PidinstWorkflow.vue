<template>
  <Workflow
      :submission=props.submission
      :pid-name=pidName
      :api-url-data-cite=apiUrlDataCite
  ></Workflow>
</template>

<script setup>
import {ref} from "vue";
import Workflow from './Workflow.vue';

const props = defineProps({
  submission: {type: Object, required: true}
});

const pidName = ref('pidinst');
const apiUrlDataCite = ref('https://api.datacite.org/dois?fields[dois]=titles,creators,publisher,publicationYear&query=types.resourceTypeGeneral:Instrument');
</script>
