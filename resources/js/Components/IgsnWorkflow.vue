<template>
	<h1>--IgsnWorkflow.vue--</h1>
</template>

<script setup>
const {useLocalize} = pkp.modules.useLocalize;

const {t, localizeSubmission} = useLocalize();
const props = defineProps({submission: {type: Object, required: true}});
</script>

<style scoped>
h3 {
	margin-top: var(--spacing-8);
}
</style>
