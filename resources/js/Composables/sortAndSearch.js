import { ref } from 'vue';
import {useForm} from "@inertiajs/vue3";
//import { useForm } from '@inertiajs/inertia-vue3';

export function sortAndSearch(routeName, initialParams ={}) {
    const form = useForm({});
    const search = ref('');
    const sortKey = ref('id');
    const sortDirection = ref('desc');
    const params = ref({ ...initialParams });

    const updateParams = (newParams) => {
        params.value = { ...params.value, ...newParams };
    };

    const fetchData = (additionalParams = {}) => {

        const requestData = {
            ...params.value,
            ...additionalParams,
            search: search.value,
            sort_by: sortKey.value,
            sort_direction: sortDirection.value
        };
        // requestData['search'] = search.value;
        // requestData['sort_by'] = sortKey.value;
        // requestData['sort_direction'] = sortDirection.value;

        const urlParams = new URLSearchParams(window.location.search);
        const currentPage = urlParams.get('page');
        if (currentPage) {
            requestData.page = currentPage;
        }

        form.get(route(routeName, requestData), {
            preserveState: true,
            replace: true
        });
    };

    const sort = (field, isSort=true) => {
        if(isSort){
            if (sortKey.value === field) {
                sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
            } else {
                sortDirection.value = 'asc';
                sortKey.value = field;
            }
            fetchData();
        }
    };

    return { form, search, sort, fetchData, sortKey, sortDirection, updateParams };
}
