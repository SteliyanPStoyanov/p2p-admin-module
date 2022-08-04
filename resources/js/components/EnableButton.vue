<template>
    <a :href="buttonConfig.url + '/enable/' + buttonConfig.id" :class="classes"
        role="button" aria-pressed="true" title="Enable">
        <span>
            <i class="fa fa-arrow-up" aria-hidden="true"></i>
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
                'btn-success': true,
                'btn-extra-sm': true,
            }),
        },
    }
};
</script>
