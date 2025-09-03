import PidinstWorkflow from './Components/PidinstWorkflow.vue';

pkp.registry.registerComponent('PidinstWorkflow', PidinstWorkflow);

pkp.registry.storeExtend('workflow', (piniaContext) => {
	const workflowStore = piniaContext.store;

	// Extending workflow menu
	workflowStore.extender.extendFn('getMenuItems', (menuItems, args) => {
		return [
			...menuItems,
			{
				key: 'pidinst',
				label: 'PIDINST',
				state: {primaryMenuItem: 'pidinst'},
			},
		];
	});

	// Render custom component in existing menu
	workflowStore.extender.extendFn('getPrimaryItems', (primaryItems, args) => {
		if (
			args?.selectedMenuState?.primaryMenuItem === 'pidinstWorkflow' &&
			args?.selectedMenuState?.stageId ===
				pkp.const.WORKFLOW_STAGE_ID_SUBMISSION
		) {
			return [
				...primaryItems,
				{
					component: 'PidinstWorkflow',
					props: {submission: args.submission},
				},
			];
		} else {
			return primaryItems;
		}
	});

	// Render custom component in custom menu
	workflowStore.extender.extendFn('getPrimaryItems', (primaryItems, args) => {
		if (args?.selectedMenuState?.primaryMenuItem === 'pidinst') {
			return [
				{
					component: 'PidinstWorkflow',
					props: {submission: args.submission},
				},
			];
		}
		return primaryItems;
	});
});
