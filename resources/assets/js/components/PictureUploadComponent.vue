<template>
    <div v-bind:style="size" @dragover="dragover" @dragleave="dragleave" @drop.stop.prevent="alert(1)" @paste.stop.prevent="alert(1)" @click="click">
        <img v-if="value" :src="value" class="autoresize"/>
        <input type="file" @change="onFileChanged" style="display:none" ref="SelectFile" />
    </div>
</template>

<script>
    export default {
        name: "PictureUploadComponent",
        props:['value','width','height'],
        computed:{
            size: function(){
                return {
                    width: this.width,
                    height: this.height,
                    border: this.border
                }
            }
        },
        data() {
            return {
                border:"4px solid black"
            }
        },
        methods:{
            dragover(){
                this.border = "2px dashed gray"
            },
            dragleave() {
                this.border = "4px solid black"
            },
            drop(event) {
                console.log('ura');
                this.startUpload(e.originalEvent.dataTransfer.files[0])
                this.dragleave()
            },
            click() {
                this.$refs.SelectFile.click();
            },
            onFileChanged(){
                if (this.$refs.SelectFile.files.length>0) {
                    this.startUpload(this.$refs.SelectFile.files[0])
                }
            },
            startUpload(file){
                var formData = new FormData();
                formData.append('image', file)
                axios({
                    url: 'load-image',
                    method: 'POST',
                    data: formData
                })
                    .then((result) => {
                        this.$emit('input',result.data.url);
                    })
                    .catch((err) => {
                        console.log(err);
                    })
            }
        }
    }
</script>

<style scoped>
    img.autoresize {
        max-width:100%;
        height:auto;
    }
</style>