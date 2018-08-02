<template>
    <div class="container">
        <div class="row">
            <div class="col">{{value.article.name}}</div>
            <div class="col">{{value.place.name}}</div>
            <div class="col">{{value.lector.name}}</div>
        </div>
        <div class="row" v-if="!value.expanded">
            <div class="col">{{value.article.short_text}}</div>
        </div>
        <div class="row">
            <div class="col">Начало: {{value.start}}</div>
            <div class="col">Окончание: {{value.end}}</div>
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
        props: ['value','expanded']
    }
</script>

<style scoped>

</style>