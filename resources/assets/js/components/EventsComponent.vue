<template>
    <div class="container">
        <div class="row border" v-for="(kino_event,index) in kino_events" @click="expand(index)">
            <event-component  v-model="kino_events[index]"></event-component>
        </div>
    </div>
</template>

<script>
    import EventComponent from "./EventComponent.vue"
    export default {
        name: "EventsComponent",
        components: {
            EventComponent
        },
        data(){
            return {
                kino_events:[],
                index:0
            }
        },
        mounted() {
            axios.get('kino-event')
                .then(response => {
                    this.kino_events = response.data
                    this.kino_events[0].expanded = true;
                })
                .catch(error => {
                    console.log(error)
                })
        },
        methods:{
            expand(index) {
                if (index != this.index) {
                    console.log('change');
                    this.kino_events[this.index].expanded = false;
                    this.index = index;
                    this.kino_events[this.index].expanded = true;
                }
            }
        }
    }
</script>

<style scoped>

</style>