<template>
    <div>
        <EditButton :buttonConfig="buttonConfig"></EditButton>
        <span v-if="data.is_active == 0">
            <EnableButton :buttonConfig="buttonConfig"></EnableButton>
        </span>
        <span v-else>
            <DisableButton :buttonConfig="buttonConfig"></DisableButton>
        </span>
        <DeleteButton :buttonConfig="buttonConfig"></DeleteButton>
    </div>
</template>

<script>
import EditButton from './EditButton.vue';
import EnableButton from './EnableButton.vue';
import DeleteButton from './DeleteButton.vue';
import DisableButton from './DisableButton.vue';

export default {
    components: {
        EditButton,
        EnableButton,
        DeleteButton,
        DisableButton
    },
    data: function() {
        return {
            buttonConfig: {
                url: window.location.pathname,
                id: this.data.administrator_id
            }
        }
    },
    props: {
        data: {},
        name: {},
        click: {},
        meta: {},
        classes: {},
    },
};
</script>
