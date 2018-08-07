<template>
    <vue-editor
            v-model="val"
            :editorOptions="editorSettings"
            :customModules="customModulesForEditor"
            useCustomImageHandler
            @imageAdded="handleImageAdded"
            ref="myTextEditor"
    ></vue-editor>

</template>

<script>

    import { VueEditor } from 'vue2-editor'
    import { ImageDrop } from 'quill-image-drop-module'
    import ImageResize  from 'quill-image-resize-module'
    export default {
        props:['value'],
        components: {
            VueEditor
        },
        watch: {
            value: function(newVal, oldVal) { // watch it
                if (this.aga) {
                    this.val = newVal;
                    this.aga = false;
                } else {
                    this.aga = true;
                }
            },
            val: function() {
                this.aga = false;
                this.$emit('input',this.val);
            }
        },
        data() {
            return {
                aga:true,
                val:this.value,
                customModulesForEditor: [
                    { alias: 'imageDrop', module: ImageDrop },
                    { alias: 'imageResize', module: ImageResize }
                ],
                editorSettings: {
                    modules: {
                        imageDrop: true,
                        imageResize: {},
                        toolbar: {container: [
                                ['bold', 'italic', 'underline', 'strike' ],
                                [{ 'size': ['small', false, 'large', 'huge'] }],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                ['image', 'video', 'code-block'],
                                [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                                [{ 'font': [] }],
                                [{ 'align': [] }],
                                ['clean'],
                                ['omega']
                            ],
                            handlers: {
                                'omega': () => {
                                    var range = this.editor.getSelection();
                                    if (range) {
                                        this.editor.insertText(range.index, "Ω");
                                    }
                                }
                            }
                        }
                    },
                    theme: 'snow'
                }
            }
        },
        computed: {
            editor() {
                return this.$refs.myTextEditor.quill
            },
        },
        methods: {
            handleImageAdded: function(file, Editor, cursorLocation, resetUploader) {
                // An example of using FormData
                // NOTE: Your key could be different such as:
                // formData.append('file', file)

                var formData = new FormData();
                formData.append('image', file)

                axios({
                    url: 'load-image',
                    method: 'POST',
                    data: formData
                })
                    .then((result) => {
                        let url = result.data.url // Get url from response
                        Editor.insertEmbed(cursorLocation, 'image', url);
                        resetUploader();
                    })
                    .catch((err) => {
                        console.log(err);
                    })
            },

        }
    }
</script>
