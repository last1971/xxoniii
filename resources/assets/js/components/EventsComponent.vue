<template>
    <div class="container">
        <div class="row btn-light" v-if="editMode && (kino_events.length == 0 || kino_events[0].id!==0)" @click="makeNewEvent">Новый ивент</div>
        <event-edit-component v-if="editMode" class="row border" v-model="kino_events[index]" @rescan="scan" ref="editor"></event-edit-component>
        <div class="row border" v-for="(kino_event,index) in kino_events" @click="expand(index)">
            <event-component  v-model="kino_events[index]"></event-component>
        </div>
    </div>
</template>

<script>
    import EventComponent from "./EventComponent.vue"
    import EventEditComponent from "./EventEditComponent";
    export default {
        name: "EventsComponent",
        components: {
            EventEditComponent,
            EventComponent
        },
        data(){
            return {
                kino_events:[],
                index:0,
                editMode:false,
                newEvent:{
                    id:0,
                    article_id:0,
                    article:{id:0,name:'',short_text:'',content:''},
                    lector_id:0,
                    lector:{id:0,name:''},
                    place_id:0,
                    place:{id:0,name:''},
                    start:new Date().toISOString().slice(0, 19).replace('T', ' '),
                    end:new Date().toISOString().slice(0, 19).replace('T', ' '),
                    seat_count:0,
                    seat_free:0,
                    expanded: false
                },
            }
        },
        created: function () {
            window.addEventListener('keyup', this.editToggle)
        },
        mounted() {
            this.scan()
        },
        methods:{
            scan(){
                axios.get('kino-event')
                    .then(response => {
                        this.kino_events = response.data
                        this.kino_events[0].expanded = true;
                    })
                    .catch(error => {
                        console.log(error)
                    })
            },
            expand(index) {
                if (index != this.index) {
                    this.kino_events[this.index].expanded = false;
                    this.index = index;
                    this.kino_events[this.index].expanded = true;
                }
            },
            editToggle(event){
                if (this.$refs.editor)
                    this.$refs.editor.clearErrors()
                if (event.key=='F4')
                    this.editMode = !this.editMode;
            },
            makeNewEvent(){
                this.kino_events.unshift(Object.assign({},this.newEvent))
                this.index=0;
            }
        }
    }
</script>

<style scoped>

</style>