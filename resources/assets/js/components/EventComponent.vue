<template>
    <div class="container">
        <div class="row">
            <a v-if="value.web" :href="value.web" class="col">{{value.article.name}}</a>
            <div v-else class="col">{{value.article.name}}</div>
            <div class="col">{{value.place.name}}</div>
            <div class="col">{{value.lector.name}}</div>
        </div>
        <div class="row" v-if="!value.expanded">
            <div class="col">{{value.article.short_text}}</div>
        </div>
        <div class="row">
            <div class="col">Начало: {{make_date(value.start).toLocaleString("ru", dateOptions)}}</div>
            <div class="col">Окончание: {{make_date(value.end).toLocaleString("ru", dateOptions)}}</div>
            <div class="col">Всего мест: {{value.seat_count}}</div>
            <div class="col">Из них свободно: {{value.seat_free}}</div>
        </div>
        <div class="row" v-if="value.expanded">
            <div class="col" v-html="value.article.content"></div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "EventComponent",
        props: ['value','expanded'],
        data(){
            return {
                dateOptions : {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    weekday: 'long',
                    timezone: 'UTC',
                    hour: 'numeric',
                    minute: 'numeric',
                    second: 'numeric'
                }
            }
        },
        methods:{
            make_date(value){
                let year = value.substr(0,4)
                let month = parseInt(value.substr(5,2)) - 1
                let day = value.substr(8,2)
                let hour = value.substr(11,2)
                let minute = value.substr(14,2)
                return new Date(year,month,day,hour,minute)
            }
        }
    }
</script>

<style scoped>

</style>