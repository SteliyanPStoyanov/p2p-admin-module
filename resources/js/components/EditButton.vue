<template>
    <a :href="buttonConfig.url + '/edit/' + buttonConfig.id" :class="classes"
        role="button" aria-pressed="true" title="Edit">
        <span>
            <i class="fa fa-edit" aria-hidden="true"></i>
        </span>
    </a>
</template>

<script>
export default {
    props: {
        buttonConfig: {
            id: '',
            url: '',
        },
        data: {},
        name: {},
        click: {
            type: Function,
            default: () => {}
        },
        classes: {
            type: Object,
            default: () => ({
                'btn': true,
                'btn-primary': true,
                'btn-extra-sm': true,
            }),
        },
    }
};
</script>
