import IgsnWorkflow from './Components/PidinstWorkflow.vue';

pkp.registry.registerComponent('PidinstWorkflow', IgsnWorkflow);

pkp.registry.storeExtend('workflow', (piniaContext) => {
    const workflowStore = piniaContext.store;

    // Extending workflow menu
    workflowStore.extender.extendFn('getMenuItems', (menuItems, args) => {
        const publicationsItem = menuItems.find(item => item.key === "publication");
        publicationsItem.items.push({
            key: 'pidinst',
            label: 'PIDINST',
            state: {
                primaryMenuItem: 'pidinst',
                title: 'PIDINST'
            }
        });
        return menuItems;
    });

    // Render custom component in custom menu
    workflowStore.extender.extendFn('getPrimaryItems', (primaryItems, args) => {
        if (args?.selectedMenuState?.primaryMenuItem === 'pidinst') {
            return [
                {
                    component: 'PidinstWorkflow',
                    props: {
                        submission: args.submission
                    },
                },
            ];
        }
        return primaryItems;
    });
});
