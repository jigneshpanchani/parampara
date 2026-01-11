<script>
export default {
    props: {
        data: {
            type: Array,
            required: true, // Pass the data array here
        },
        colspan: {
            type: Number,
            default: 4, // Default colspan value
        },
        message: {
            type: String,
            default: 'No Records Found...', // Default message
        },
    },
    computed: {
        noRecords() {
            // Check if there are no records in the pass data
            return this.data.length === 0;
        },
    },
};
</script>

<template>
    <tr v-if="noRecords">
        <td :colspan="colspan" class="py-4 text-center text-sm text-red-500 font-semibold">
            {{ message }}
        </td>
    </tr>
</template>
