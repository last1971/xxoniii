<template>
    <div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">Редактируемый Ивент</div>
            <div class="text-danger text-md-right col-md-6" v-if="errors.message">
                <em>
                    {{ errors.message }}
                </em>
            </div>
            <div class="text-success text-md-right col-md-6" v-if="success">
                <em>
                    Данные изменены
                </em>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">Название</label>
            <input required name="name" type="text" class="col-md-4 form-control" v-model="value.article.name"
                   v-bind:class="{ 'is-invalid' : errors.errors !== undefined && errors.errors['article.name'] !== undefined }"/>
            <span class="invalid-feedback col-md-4 text-md-right" v-if="errors.errors !== undefined && errors.errors['article.name'] !== undefined">
                <strong>{{errors.errors['article.name'][0]}}</strong>
            </span>
        </div>
        <div class="form-group row">
            <label for="short_text" class="col-md-4 col-form-label text-md-right">Описане</label>
            <input required name="short_text" type="text" class="col-md-4 form-control" v-model="value.article.short_text"
                   v-bind:class="{ 'is-invalid' : errors.errors !== undefined && errors.errors['article.short_text'] !== undefined }"/>
            <span class="invalid-feedback col-md-4 text-md-right" v-if="errors.errors !== undefined && errors.errors['article.short_text'] !== undefined">
                <strong>{{errors.errors['article.short_text'][0]}}</strong>
            </span>
        </div>
        <html-editor-component class="form-control" v-model="value.article.content"></html-editor-component>
        <div class="form-group row">
            <label for="start" class="col-md-4 col-form-label text-md-right">Начало</label>
            <date-picker :datetime="true"  :calendar-button="true" name="start" type="text" class="col-md-4 form-control" v-model="start"
                              v-bind:class="{ 'is-invalid' : errors.errors !== undefined && errors.errors.start !== undefined }"></date-picker>
            <span class="invalid-feedback col-md-4 text-md-right" v-if="errors.errors !== undefined && errors.errors.start !== undefined"><strong>{{errors.errors.start[0]}}</strong></span>
        </div>
        <div class="form-group row">
            <label for="end" class="col-md-4 col-form-label text-md-right" >Окончание</label>
            <date-time-picker required name="end" type="text" class="col-md-4 form-control" v-model="end"
                   v-bind:class="{ 'is-invalid' : errors.errors !== undefined && errors.errors.end !== undefined }"></date-time-picker>
            <span class="invalid-feedback col-md-4 text-md-right" v-if="errors.errors !== undefined && errors.errors.end !== undefined"><strong>{{errors.errors.end[0]}}</strong></span>

        </div>
        <div class="form-group row">
            <label for="place" class="col-md-4 col-form-label text-md-right">Место</label>
            <select required name="place" class="col-md-4 form-control" v-model="value.place"
                    v-bind:class="{ 'is-invalid' : errors.errors !== undefined && errors.errors['place.id'] !== undefined }">
                <option v-for="place in places" :value="place">{{place.name}}</option>
            </select>
            <span class="invalid-feedback col-md-4 text-md-right" v-if="errors.errors !== undefined && errors.errors['place.id'] !== undefined">
                <strong>{{errors.errors['place.id'][0]}}</strong>
            </span>
        </div>
        <div class="form-group row">
            <label for="lector" class="col-md-4 col-form-label text-md-right">Лектор</label>
            <select required name="lector" class="col-md-4 form-control" v-model="value.lector"
                    v-bind:class="{ 'is-invalid' : errors.errors !== undefined && errors.errors['lector.id'] !== undefined }">
                <option v-for="lector in lectors" :value="lector">{{lector.name}}</option>
            </select>
            <span class="invalid-feedback col-md-4 text-md-right" v-if="errors.errors !== undefined && errors.errors['lector.id'] !== undefined">
                <strong>{{errors.errors['lector.id'][0]}}</strong>
            </span>
        </div>
        <div class="form-group row">
            <label for="web" class="col-md-4 col-form-label text-md-right">Сайт</label>
            <input name="web" type="text" class="col-md-4 form-control" v-model="value.web" />
        </div>
        <div class="form-group row">
            <div class="col-md-4 btn alert-success" @click="save">Сохранить</div>
            <div class="col-md-4 btn alert-danger" @click="cancel">Отменить</div>
        </div>
    </div>
    </div>
</template>

<script>
    import HtmlEditorComponent from "./HtmlEditorComponent";
    import DateTimePicker from "simple-vue2-datetimepicker"
    import DatePicker from "vuejs-datepicker"
    export default {
        name: "EventEditComponent",
        components: {HtmlEditorComponent,DateTimePicker,DatePicker},
        props: ['value'],
        data(){
            return {
                errors: {},
                success: false,
                places:[],
                lectors:[],
            }
        },
        computed:{
            end : {
                get() {
                    return new Date(this.value.end)
                },
                set(value) {
                    this.value.end = value.toISOString().slice(0, 19).replace('T', ' ');
                }
            },
            start : {
                get() {
                    return new Date(this.value.start)
                },
                set(value) {
                    this.value.end = value.toISOString().slice(0, 19).replace('T', ' ');
                }
            }

        },
        mounted(){
            axios.get('place')
                .then(response => {
                    this.places = response.data
                })
                .catch(error => {
                    console.log(error)
                })
            axios.get('lector')
                .then(response => {
                    this.lectors = response.data
                })
                .catch(error => {
                    console.log(error)
                })

        },
        watch:{
            value : function(neVal, oldVal){
                this.clearErrors()
            }
        },
        methods:{
            clearErrors () {
                this.errors = [];
                this.success = false;
            },
            save(){
                this.clearErrors();
                axios.put('kino-event/'+this.value.id,this.value)
                    .then(response => {
                        this.success = true;
                        this.$emit('rescan');
                    }).catch(error => {
                        this.success = false;
                        this.errors =  error.response.data;
                    }
                )
            },
            cancel () {
                this.$emit('rescan');
            },
        }
    }
</script>

<style scoped>

</style>