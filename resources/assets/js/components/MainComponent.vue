<template>
    <div class="container">
        <div class="row mt-2">
            <div class="col-md-1 pt-1">
                Период:
            </div>
            <div class="col-md-2">
                <datepicker
                        input-class="form-control"
                        :language="ru"
                        v-model="from"
                        :monday-first="true"
                        @input="dateSelect"
                        :disabled-dates="disabledDates"
                ></datepicker>
            </div>
            <div class="col-md-2">
                <datepicker
                        input-class="form-control"
                        :language="ru"
                        v-model="to"
                        :monday-first="true"
                        @input="dateSelect"
                        :disabled-dates="disabledDates"
                ></datepicker>
            </div>
            <div class="col-md-2 input-group mt-2">
                <input class="form-control" type="radio" v-model="type_days" value="all"/><label>Все</label>
                <input class="form-control" type="radio" v-model="type_days" value="monday"/><label>Будни</label>
                <input class="form-control" type="radio" v-model="type_days" value="holidays"/><label>Выходные</label>
            </div>
            <div class="col-md-3 input-group mt-2" >
                <div v-for="i in 7">
                    <input class="form-control" v-model="weekday_values[i-1]" type="checkbox" @change="getData">
                    <label>{{weekday_names[i-1]}}</label>
                </div>
            </div>
            <div class="col-md-2  mt-2">
                <div class="input-group">
                    <label for="time_from" class="d-md-inline-block">с </label>
                    <input id="time_from" class="form-control d-md-inline-block" v-model="time_from" type="number" min="0" max="24" @change="getData">

                    <label for="time_to" class="d-md-inline-block">по </label>
                    <input id="time_to" class="form-control d-md-inline-block" v-model="time_to" type="number" min="0" max="24" @change="getData">
                </div>
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
                theaters: [],
                type_days: 'all',
                weekday_names: ['пн.','вт.','ср.','чт.','пт.','сб.','вс.'],
                weekday_values: [true, true, true, true, true, true, true],
                time_from: 0,
                time_to: 24
            }
        },
        mounted() {
            this.to = new Date()
            this.to = new Date(this.to.setDate(this.to.getDate() - 1))
            this.disabledDates = { to: this.from, from: this.to }
            this.getData()
        },
        watch : {
            type_days() {
                this.getData()
            }
        },
        methods: {
            dateSelect(date) {
                if (this.to.getTime() < this.from.getTime()) {
                    this.to = this.from
                }
                this.getData()
            },
            getData() {
                axios.get('/api/theaters', { params :
                        {
                            from: this.from,
                            to: this.to,
                            days: this.type_days,
                            weekdays: this.weekday_values,
                            time_from: this.time_from,
                            time_to: this.time_to
                        }
                    })
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