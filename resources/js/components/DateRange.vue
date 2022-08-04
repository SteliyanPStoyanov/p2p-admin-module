<template>
    <input type="text" class="form-control" ref="daterange"
        placeholder="Creation date" :name="value.name"
        :v-model="value.model" @input="$emit('input', $event.target.value)"/>
</template>

<script>
// https://www.daterangepicker.com

import $ from 'jquery';
import 'daterangepicker/daterangepicker.js';
import 'daterangepicker/daterangepicker.css';
import moment from 'moment';

export default {

    props: {
        value: {}, // name and model are mandatory fields
        start: {
            type: String,
        },
        end: {
            type: String,
        },
    },

    mounted() {
        let startDate;
        let endDate;

        if (this.start && this.end) {
            startDate = moment(this.start, 'DD-MM-YYYY');
            endDate = moment(this.end, 'DD-MM-YYYY');
        } else {
            // startDate = moment().subtract(29, 'days');
            // endDate = moment();
        }

        const onDateSelected = (start, end) => {
            this.$emit('change', {
                startDate: start.format('DD-MM-YYYY'),
                endDate: end.format('DD-MM-YYYY'),
            });
        };

        $(this.$refs.daterange).daterangepicker(
            {
                autoApply: false,
                startDate: startDate,
                endDate: endDate,
                locale: {
                    format: 'DD-MM-YYYY'
                }
                /*ranges: {
                'Depuis 1 mois': [moment().subtract(1, 'month'), moment()],
                'Depuis 3 mois': [moment().subtract(3, 'month'), moment()],
                'Depuis 6 mois': [moment().subtract(6, 'month'), moment()],
                'Depuis 1 an': [moment().subtract(1, 'year'), moment()],
                },*/
            },
            onDateSelected
        );

        // $(this.$refs.daterange).on('apply.daterangepicker', function(ev, picker) {
        //     picker.handleInput(ev);
        // });

    },

    computed: {
        formattedResult() {
            if (!this.start && !this.end) return '';
            return this.start + ' - ' + this.end;
        },
    },

};
</script>
