<template>
    <div>
        <input v-model="year"/>
    </div>
</template>

<script>
    export default {
        name: "DatePickerComponent",
        props:["value"],
        computed:{
            year:{
                get(){
                    return this.value.getFullYear()
                },
                set(value){
                    this.value.setFullYear(value)
                    this.$emit('input',this.value)
                }
            }
        }
    }
</script>

<style scoped>

</style>