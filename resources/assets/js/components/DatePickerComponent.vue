<template>
    <div>
        <select v-model="day">
            <option v-for="month_day in days">{{month_day}}</option>
        </select>
        <select v-model="month">
            <option v-for="(month_,index) in monthes" :value="index">{{month_}}</option>
        </select>
        <input v-model="year">
        <select v-model="hour">
            <option v-for="hour_ in 23" :value="hour_">{{hour_}}</option>
        </select>
        <span> : </span>
        <select v-model="minute">
            <option v-for="minute_ in 59">{{minute_}}</option>
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
                    this.value.setHours(value)
                    this.$emit('input', this.value)
                }
            },
            minute: {
                get() {
                    return this.value.getMinutes()
                },
                set(value) {
                    this.value.setMinutes(value)
                    this.$emit('input', this.value)
                }
            },
            day: {
                get() {
                    return this.value.getDate()
                },
                set(value) {
                    this.value.setDate(value)
                    this.$emit('input', this.value)
                }
            },
            month: {
                get() {
                    return this.value.getMonth()
                },
                set(value) {
                    this.value.setMonth(value)
                    this.$emit('input', this.value)
                }
            },
            year: {
                get() {
                    return this.value.getFullYear()
                },
                set(value) {
                    if (value>1900) {
                        this.value.setFullYear(value)
                        this.$emit('input', this.value)
                    }
                }
            },
            days: {
                get() {
                    let days = this.days_[this.month]
                    if (this.year % 4 == 0) days++
                    return days
                }
            }
        }
    }
</script>

<style scoped>

</style>