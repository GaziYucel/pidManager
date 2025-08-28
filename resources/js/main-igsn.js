import IgsnWorkflow from './Components/IgsnWorkflow.vue';

pkp.registry.registerComponent('IgsnWorkflow', IgsnWorkflow);

pkp.registry.storeExtend('workflow', (piniaContext) => {
	console.log('IgsnWorkflow', piniaContext);

	const workflowStore = piniaContext.store;

	// Extending workflow menu
	workflowStore.extender.extendFn('getMenuItems', (menuItems, args) => {
		return [
			...menuItems,
			{
				key: 'igsn',
				label: 'IGSN',
				state: {primaryMenuItem: 'igsn'},
			},
		];
	});

	// Render custom component in existing menu
	workflowStore.extender.extendFn('getPrimaryItems', (primaryItems, args) => {
		if (
			args?.selectedMenuState?.primaryMenuItem === 'igsnWorkflow' &&
			args?.selectedMenuState?.stageId ===
				pkp.const.WORKFLOW_STAGE_ID_SUBMISSION
		) {
			return [
				...primaryItems,
				{
					component: 'IgsnWorkflow',
					props: {submission: args.submission},
				},
			];
		} else {
			return primaryItems;
		}
	});

	// Render custom component in custom menu
	workflowStore.extender.extendFn('getPrimaryItems', (primaryItems, args) => {
		if (args?.selectedMenuState?.primaryMenuItem === 'igsn') {
			return [
				{
					component: 'IgsnWorkflow',
					props: {submission: args.submission},
				},
			];
		}
		return primaryItems;
	});
});

