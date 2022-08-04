<template>
    <a :href="buttonConfig.url + '/disable/' + buttonConfig.id" :class="classes"
        role="button" aria-pressed="true" title="Disable">
        <span>
            <i class="fa fa-arrow-down" aria-hidden="true"></i>
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
                'btn-secondary': true,
                'btn-extra-sm': true,
            }),
        },
    }
};
</script>
