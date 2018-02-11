<template>
    <div>
        <button @click.prevent="byPrice = !byPrice">
            {{ byPrice ? 'Sort by id' : 'Sort by price' }}
        </button>
        <button @click.prevent="$emit('create-new')">Create new good</button>
        <table>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Edit</th>
            </tr>
            <tr v-for="(good, i ) in goods">
                <td>{{ i + 1 }}</td>
                <td>{{ good.id }}</td>
                <td><a href="#" @click.prevent="$emit('clicked', good.id)">{{ good.name }}</a></td>
                <td>{{ good.price }}</td>
                <td><a href="#" @click.prevent="$emit('edit', good.id)">Edit</a></td>
            </tr>
        </table>
    </div>
</template>
<script>
import axios from 'axios'

export default {
    name: 'goods-list',

    data () {
        return {
            goods: [],
            byPrice: false,
            offset: 0
        }
    },

    watch: {
        async byPrice () {
            await this.loadData()
        },

        async offset () {
            await this.loadData()
        }
    },

    async mounted () {
        await this.loadData()
    },

    methods: {
        async loadData () {
            const url = `/api/goods/${this.byPrice ? 'byprice' : 'byid'}?offset=${this.offset}`
            const { data } = await axios.get(url)

            this.goods = data
        }    
    }
}
</script>

