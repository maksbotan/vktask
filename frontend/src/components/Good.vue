<template>
    <div>
        <a href="#"
           @click.prevent="$emit('go-back')">
            Go back
        </a>
        <div v-if="good">
            <h1>{{ good.name }}</h1>
            <p>{{ good.description }}</p>
            <p>Price: {{ good.price }}</p>
            <img :src="good.pic_url"/>
        </div>
    </div>
</template>
<script>
import axios from 'axios'

export default {
    name: 'good',

    props: {
        goodID: {
            type: Number
        }
    },

    data () {
        return {
            good: null
        }
    },

    async mounted () {
        const { data } = await axios.get(`/api/good/${this.goodID}`)
        this.good = data
    }
}
</script>

