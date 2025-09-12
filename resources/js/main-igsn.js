import IgsnWorkflow from './Components/IgsnWorkflow.vue';

pkp.registry.registerComponent('IgsnWorkflow', IgsnWorkflow);

pkp.registry.storeExtend('workflow', (piniaContext) => {
    const workflowStore = piniaContext.store;

    // Extending workflow menu
    workflowStore.extender.extendFn('getMenuItems', (menuItems, args) => {
        const publicationsItem = menuItems.find(item => item.key === "publication");
        publicationsItem.items.push({
            key: 'igsn',
            label: 'IGSN',
            state: {
                primaryMenuItem: 'igsn',
                title: 'IGSN'
            }
        });
        return menuItems;
    });

    // Render custom component in custom menu
    workflowStore.extender.extendFn('getPrimaryItems', (primaryItems, args) => {
        if (args?.selectedMenuState?.primaryMenuItem === 'igsn') {
            return [
                {
                    component: 'IgsnWorkflow',
                    props: {
                        submission: args.submission
                    },
                },
            ];
        }
        return primaryItems;
    });
});
