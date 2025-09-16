import IgsnWorkflow from './IgsnWorkflow.vue';
import {computed} from "vue";

pkp.registry.registerComponent('IgsnWorkflow', IgsnWorkflow);

pkp.registry.storeExtend('workflow', (piniaContext) => {
    const workflowStore = piniaContext.store;

    // Extending workflow menu
    workflowStore.extender.extendFn('getMenuItems', (menuItems) => {
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
        const publication = computed(() => {
            if (args.selectedPublication) {
                return args.selectedPublication;
            } else {
                return args.submission?.publications?.find((item) => item.id === args.submission.currentPublicationId);
            }
        });

        if (args?.selectedMenuState?.primaryMenuItem === 'igsn') {
            return [
                {
                    component: 'IgsnWorkflow',
                    props: {
                        publication: publication
                    },
                },
            ];
        }
        return primaryItems;
    });
});
