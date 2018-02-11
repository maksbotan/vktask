<template>
  <div id="app">
    <login v-if="state === 'auth'" @logged-in="state = 'list'"/>
    <goods-list v-if="state === 'list'" @clicked="select"/>
    <good v-if="state === 'good'"
          :goodID="selectedGoodID"
          @go-back="state = 'list'; selectedGoodID = null"
    />
  </div>
</template>
<script>
import axios from 'axios'

import Login from './components/Login'
import GoodsList from './components/GoodsList'
import Good from './components/Good'

export default {
    name: 'app',

    components: {Login, GoodsList, Good},

    data () {
        return {
            state: 'auth',
            selectedGoodID: null
        }
    },

    async mounted () {
        // We will store auth token in localStorage
        // If it's not present there, show simple login form

        const token = localStorage.getItem('vktask-auth-token')
        if (!token) {
            return
        }

        axios.defaults.headers.common['Authorization'] = 'Token ' + token;
        this.state = 'list'
    },

    methods: {
        select (goodID) {
            this.selectedGoodID = goodID
            this.state = 'good'
        }
    }

}
</script>

