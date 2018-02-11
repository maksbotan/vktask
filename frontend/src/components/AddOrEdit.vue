<template>
    <div>
        <a href="#"
           @click.prevent="$emit('go-back')">
            Go back
        </a>
        <h1 v-if="existingGoodID">Editing good {{ existingGoodID }}</h1>
        <h1 v-else>Creating new good</h1>

        <p>Name: <input type="text" v-model="good.name"/></p>
        <p>Description: <textarea v-model="good.description"></textarea></p>
        <p>Price: <input type="number" min="1" v-model.number="good.price"/></p>
        <p>Picture url: <input type="text" v-model="good.pic_url"/></p>

        <button type="button" @click.prevent="save">Save</button>
        <button type="button" v-if="existingGoodID" @click.prevent="remove">Remove</button>
    </div>
</template>
<script>
import axios from 'axios'

export default {
    name: 'add-or-edit',

    props: {
        existingGoodID: {
            type: Number,
            default: null
        }
    },

    data () {
        return {
            good: {
                name: '',
                description: '',
                price: 0,
                pic_url: ''
            }
        }
    },

    async mounted () {
        if (!this.existingGoodID) {
            return;
        }

        const { data } = await axios.get(`/api/good/${this.existingGoodID}`)
        this.good = data
    },

    methods: {
        async save () {
            let resultID = this.existingGoodID

            if (!this.existingGoodID) {
                const { data } = await axios.post('/api/good/new', this.good)
                resultID = data.id
            } else {
                await axios.put(`/api/good/${this.existingGoodID}`, this.good)
            }

            this.$emit('done', resultID)
        },

        async remove () {
            if (!this.existingGoodID) {
                return
            }

            await axios.delete(`/api/good/${this.existingGoodID}`)

            this.$emit('done', null)
        }
    }
}
</script>

