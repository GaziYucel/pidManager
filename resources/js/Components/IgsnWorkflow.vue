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

const pidName = ref('igsn');
const apiUrlDataCite = ref('https://api.datacite.org/dois?fields[dois]=titles,creators,publisher,publicationYear&query=relatedIdentifiers.relatedIdentifierType:IGSN AND types.resourceTypeGeneral:PhysicalObject');
</script>
