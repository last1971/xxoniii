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
            <label for="name" class="col-md-2 col-form-label text-md-right">Название</label>
            <input required name="name" type="text" class="col-md-8 form-control" v-model="value.article.name"
                   v-bind:class="{ 'is-invalid' : errors.errors !== undefined && errors.errors['article.name'] !== undefined }"/>
            <span class="invalid-feedback col-md-2 text-md-right" v-if="errors.errors !== undefined && errors.errors['article.name'] !== undefined">
                <strong>{{errors.errors['article.name'][0]}}</strong>
            </span>
        </div>
        <div class="form-group row">
            <label for="short_text" class="col-md-2 col-form-label text-md-right">Описане</label>
            <input required name="short_text" type="text" class="col-md-8 form-control" v-model="value.article.short_text"
                   v-bind:class="{ 'is-invalid' : errors.errors !== undefined && errors.errors['article.short_text'] !== undefined }"/>
            <span class="invalid-feedback col-md-2 text-md-right" v-if="errors.errors !== undefined && errors.errors['article.short_text'] !== undefined">
                <strong>{{errors.errors['article.short_text'][0]}}</strong>
            </span>
        </div>
        <html-editor-component class="form-control" v-model="value.article.content"></html-editor-component>
        <div class="form-group row">
            <label for="start" class="col-md-2 col-form-label text-md-right">Начало</label>
            <date-picker-component  name="start"  class="col-md-8 form-control" v-model="start"
                              v-bind:class="{ 'is-invalid' : errors.errors !== undefined && errors.errors.start !== undefined }"></date-picker-component>
            <span class="invalid-feedback col-md-2 text-md-right" v-if="errors.errors !== undefined && errors.errors.start !== undefined"><strong>{{errors.errors.start[0]}}</strong></span>
        </div>
        <div class="form-group row">
            <label for="end" class="col-md-2 col-form-label text-md-right" >Окончание</label>
            <date-picker-component name="end" class="col-md-8 form-control" v-model="end"
                   v-bind:class="{ 'is-invalid' : errors.errors !== undefined && errors.errors.end !== undefined }"></date-picker-component>
            <span class="invalid-feedback col-md-2 text-md-right" v-if="errors.errors !== undefined && errors.errors.end !== undefined"><strong>{{errors.errors.end[0]}}</strong></span>

        </div>
        <div class="form-group row">
            <label for="place" class="col-md-2 col-form-label text-md-right">Место</label>
            <select required name="place" class="col-md-8 form-control" v-model="value.place"
                    v-bind:class="{ 'is-invalid' : errors.errors !== undefined && errors.errors['place.id'] !== undefined }">
                <option v-for="place in places" :value="place">{{place.name}}</option>
            </select>
            <span class="invalid-feedback col-md-2 text-md-right" v-if="errors.errors !== undefined && errors.errors['place.id'] !== undefined">
                <strong>{{errors.errors['place.id'][0]}}</strong>
            </span>
        </div>
        <div class="form-group row">
            <label for="lector" class="col-md-2 col-form-label text-md-right">Лектор</label>
            <select required name="lector" class="col-md-8 form-control" v-model="value.lector"
                    v-bind:class="{ 'is-invalid' : errors.errors !== undefined && errors.errors['lector.id'] !== undefined }">
                <option v-for="lector in lectors" :value="lector">{{lector.name}}</option>
            </select>
            <span class="invalid-feedback col-md-2 text-md-right" v-if="errors.errors !== undefined && errors.errors['lector.id'] !== undefined">
                <strong>{{errors.errors['lector.id'][0]}}</strong>
            </span>
        </div>
        <div class="form-group row">
            <label for="web" class="col-md-2 col-form-label text-md-right">Сайт</label>
            <input name="web" type="text" class="col-md-8 form-control" v-model="value.web" />
        </div>
        <picture-upload-component v-model="value.mob_small" width="150px" height="300px"></picture-upload-component>
        <picture-upload-component v-model="value.mob_big" width="300px" height="700px"></picture-upload-component>
        <picture-upload-component v-model="value.dtp_small" width="800px" height="400px"></picture-upload-component>
        <picture-upload-component v-model="value.dtp_big" width="800px" height="800px"></picture-upload-component>
        <div class="form-group row">
            <div class="col-md-6 btn alert-success" @click="save">Сохранить</div>
            <div class="col-md-6 btn alert-danger" @click="cancel">Отменить</div>
        </div>
    </div>
    </div>
</template>

<script>
    import HtmlEditorComponent from "./HtmlEditorComponent";
    import DateTimePicker from "simple-vue2-datetimepicker"
    import DatePickerComponent from "./DatePickerComponent";
    import PictureUploadComponent from "./PictureUploadComponent";
    export default {
        name: "EventEditComponent",
        components: {PictureUploadComponent, DatePickerComponent, HtmlEditorComponent,},
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
                    return this.make_date(this.value.end)
                },
                set(value) {
                    this.value.end = this.unmake_date(value)
                }
            },
            start : {
                get() {
                    return this.make_date(this.value.start)
                },
                set(value) {
                    this.value.start = this.unmake_date(value)
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
            make_date(value){
                let year = value.substr(0,4)
                let month = parseInt(value.substr(5,2)) - 1
                let day = value.substr(8,2)
                let hour = value.substr(11,2)
                let minute = value.substr(14,2)
                return new Date(year,month,day,hour,minute)
            },
            unmake_date(value) {
                let year = value.getFullYear();
                let month = (parseInt(value.getMonth()) + 1).toString()
                month = month.length == 1 ? '0' + month : month
                let day = parseInt(value.getDate()).toString()
                day = day.length == 1 ? '0' + day : day
                let hour = parseInt(value.getHours()).toString()
                hour = hour.length == 1 ? '0' + hour : hour
                let minute = parseInt(value.getMinutes()).toString()
                minute = minute.length == 1 ? '0' + minute : minute
                return year + '-' + month + '-' + day + ' ' + hour + ':' + minute
            },
            clearErrors () {
                this.errors = [];
                this.success = false;
            },
            save(){
                this.clearErrors();
                axios.put('kino-event/'+this.value.id,this.value)
                    .then(response => {
                        this.success = true;
                        this.value.id = response.data.id;
                        this.value.article_id = response.data.article_id;
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