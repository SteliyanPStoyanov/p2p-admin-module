<template>
    <a :href="buttonConfig.url + '/delete/' + buttonConfig.id"
        :class="classes" role="button" aria-pressed="true" title="Delete"
            onclick="return confirm('Are you sure want to proceed?')">
        <span>
            <i class="fa fa-remove" aria-hidden="true"></i>
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
                'btn-danger': true,
                'btn-extra-sm': true,
            }),
        },
    }
};
</script>
