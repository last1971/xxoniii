<template>
    <div>
        <select v-model="day" nsme="day_select">
            <option v-for="month_day in days" v-bind:value="month_day">{{month_day}}</option>
        </select>
        <select v-model="month">
            <option v-for="(month_,index1) in monthes" v-bind:value="index1">{{month_}}</option>
        </select>
        <input v-model="year">
        <select v-model="hour">
            <option v-for="(hour_,index2) in 24" v-bind:value="index2">{{index2}}</option>
        </select>
        <span> : </span>
        <select v-model="minute">
            <option v-for="(minute_,index3) in 60" v-bind:value="index3">{{index3}}</option>
        </select>
    </div>
</template>

<script>
    export default {
        name: "DatePickerComponent",
        props: ["value"],
        data() {
            return {
                days_: [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
                monthes: [
                    "январь",
                    "февраль",
                    "март",
                    "апрель",
                    "май",
                    "июнь",
                    "июль",
                    "август",
                    "сентябрь",
                    "октябрь",
                    "ноябрь",
                    "декабрь"
                ]
            }
        },
        computed: {
            hour: {
                get() {
                    return this.value.getHours()
                },
                set(value) {
                    this.value.setHours(parseInt(value))
                    this.$emit('input', this.value)
                }
            },
            minute: {
                get() {
                    return this.value.getMinutes()
                },
                set(value) {
                    this.value.setMinutes(parseInt(value))
                    this.$emit('input', this.value)
                }
            },
            day: {
                get() {
                    return this.value.getDate()
                },
                set(value) {
                    this.value.setDate(parseInt(value))
                    this.$emit('input', this.value)
                }
            },
            month: {
                get() {
                    return this.value.getMonth()
                },
                set(value) {
                    this.value.setMonth(parseInt(value))
                    this.$emit('input', this.value)
                }
            },
            year: {
                get() {
                    return this.value.getFullYear()
                },
                set(value) {
                    if (value>1900) {
                        this.value.setFullYear(parseInt(value))
                        this.$emit('input', this.value)
                    }
                }
            },
            days: {
                get() {
                    let days = this.days_[this.month]
                    if (parseInt(this.year) % 4 == 0) days++
                    return days
                }
            }
        }
    }
</script>

<style scoped>

</style>