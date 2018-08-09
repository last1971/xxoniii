<template>
    <div class="container" v-infinite-scroll="loadMore" infinite-scroll-disabled="busy" infinite-scroll-distance="10">
        <div class="row btn-light" v-if="editMode && (kino_events.length == 0 || kino_events[0].id!==0)" @click="makeNewEvent">Новый ивент</div>
        <div class="row border" v-for="(kino_event,ind) in kino_events" @click="expand(ind)">
            <event-edit-component v-if="editMode && index == ind" class="row border" v-model="kino_events[ind]" @rescan="rescan" ref="editor"></event-edit-component>
            <event-component v-else v-model="kino_events[ind]"></event-component>
        </div>
    </div>
</template>

<script>
    import EventComponent from "./EventComponent.vue"
    import EventEditComponent from "./EventEditComponent";
    import infiniteScroll from 'vue-infinite-scroll'
    export default {
        directives: {infiniteScroll},
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
                busy:false,
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
        methods:{
            make_date(value){
                let year = value.substr(0,4)
                let month = parseInt(value.substr(5,2)) - 1
                let day = value.substr(8,2)
                let hour = value.substr(11,2)
                let minute = value.substr(14,2)
                return new Date(year,month,day,hour,minute)
            },
            expand(index) {
                if (index != this.index) {
                    let now = new Date();
                    let updated_at = this.make_date(this.kino_events[index].updated_at);
                    updated_at.setHours(updated_at.getHours() + 3)
                    var timeDiff = Math.abs(now.getTime() - updated_at.getTime());
                    var diffMinutes = Math.ceil(timeDiff / (1000 * 60 ));
                    if (this.kino_events[index].web && now<this.make_date(this.kino_events[index].end) && diffMinutes>10) {
                        axios.get('kino-event/' + this.kino_events[index].id, {params:{check_seats:1}} )
                            .then(response => {
                                this.kino_events[index].seat_count = response.data.seat_count
                                this.kino_events[index].seat_free = response.data.seat_free
                                this.kino_events[index].updated_at = response.data.updated_at
                            })
                            .catch(error => {
                                console.log(error)
                            })
                    }
                    this.kino_events[this.index].expanded = false;
                    this.index = index;
                    this.kino_events[this.index].expanded = true;
                }
            },
            editToggle(event){
                if (this.$refs.editor && this.$refs.editor.clearErrors)
                    this.$refs.editor.clearErrors()
                if (event.key=='F4')
                    this.editMode = !this.editMode;
            },
            makeNewEvent(){
                this.kino_events.unshift({
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
                },)
                this.index=0;
            },
            loadMore(){
                this.busy = true;
                let date = this.kino_events.length == 0 ?
                    new Date('2100-01-01').toISOString().slice(0, 19).replace('T', ' ') : this.kino_events[this.kino_events.length-1].start
                setTimeout(() => {
                    axios.get('kino-event', {params: {last: date}})
                            .then(response => {
                                if (response.data.length>0) {
                                    this.kino_events = this.kino_events.concat(response.data)
                                    this.kino_events[this.index].expanded = true;
                                    this.busy = false;
                                }
                            })
                            .catch(error => {
                                console.log(error)
                            })
                    }, 1000);

            },
            rescan() {
                if (this.kino_events[this.index].id == 0 ) {
                    this.kino_events.splice(this.index, 1, {
                        id: 0,
                        article_id: 0,
                        article: {id: 0, name: '', short_text: '', content: ''},
                        lector_id: 0,
                        lector: {id: 0, name: ''},
                        place_id: 0,
                        place: {id: 0, name: ''},
                        start: new Date().toISOString().slice(0, 19).replace('T', ' '),
                        end: new Date().toISOString().slice(0, 19).replace('T', ' '),
                        seat_count: 0,
                        seat_free: 0,
                        expanded: false
                    })
                } else
                    axios.get('kino-event/' + this.kino_events[this.index].id)
                        .then(response => {
                            this.kino_events.splice(this.index, 1, response.data)
                        })
                        .catch(error => {
                            console.log(error)
                        })
            }
        }
    }
</script>

<style scoped>

</style>