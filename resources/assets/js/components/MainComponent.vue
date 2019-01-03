<template>
    <div class="container">
        <div class="row mt-2">
            <div class="col-md-1 pt-1">
                Период:
            </div>
            <div class="col-md-3">
                <datepicker
                        input-class="form-control"
                        :language="ru"
                        v-model="from"
                        :monday-first="true"
                        @input="dateSelect"
                        :disabled-dates="disabledDates"
                ></datepicker>
            </div>
            <div class="col-md-3">
                <datepicker
                        input-class="form-control"
                        :language="ru"
                        v-model="to"
                        :monday-first="true"
                        @input="dateSelect"
                        :disabled-dates="disabledDates"
                ></datepicker>
            </div>
        </div>
        <div class="row border mt-2">
            <div class="col-md-1">#</div>
            <div class="col-md-3">Кинотеатр</div>
            <div class="col-md-1">Залов</div>
            <div class="col-md-1">Мест</div>
            <div class="col-md-2">Ср.дн. выручка руб.</div>
            <div class="col-md-2">Ср.дн. заполн. %</div>
        </div>
        <div class="row border"
             v-for="(value, key) in theaters"
        >
            <div class="col-md-1 btn btn-primary" @click="toggle(key)">
                <div v-if="theaters[key].toggle">\/</div>
                <div v-else>></div>
            </div>
            <div class="col-md-3">{{key}}</div>
            <div class="col-md-1">{{value.halls}}</div>
            <div class="col-md-1">{{value.seats}}</div>
            <div class="col-md-2 text-right">{{parseInt(value.amount)}}</div>
            <div class="col-md-2 text-right">{{value.fullness.toFixed(2)}}</div>
            <div class="col-md-3"></div>
            <div class="col-12 container" v-if="value.toggle">
                <div class="row" v-for="hall in value.rows">
                    <div class="col-md-1"></div>
                    <div class="col-md-3">{{hall.name}}</div>
                    <div class="col-md-1"></div>
                    <div class="col-md-1">{{hall.seats}}</div>
                    <div class="col-md-2 text-right">{{parseInt(hall.amount)}}</div>
                    <div class="col-md-2 text-right">{{hall.fullness.toFixed(2)}}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {en, ru} from 'vuejs-datepicker/dist/locale'
    export default {
        name: "MainComponent",
        components: {Datepicker},
        data() {
            return {
                from: new Date('2018-12-07'),
                to: undefined,
                en: en,
                ru: ru,
                disabledDates: {},
                theaters: []
            }
        },
        mounted() {
            this.to = new Date()
            this.to = new Date(this.to.setDate(this.to.getDate() - 1))
            this.disabledDates = { to: this.from, from: this.to }
            this.getData()
        },
        methods: {
            dateSelect(date) {
                if (this.to.getTime() < this.from.getTime()) {
                    this.to = this.from
                }
                this.getData()
            },
            getData() {
                axios.get('/api/theaters', { params : { from: this.from, to: this.to }})
                    .then(response => {
                        this.theaters = response.data
                    })
            },
            toggle(key) {
                this.$set(this.theaters[key], 'toggle',  !this.theaters[key].toggle)
            }
        }
    }
</script>

<style scoped>

</style>