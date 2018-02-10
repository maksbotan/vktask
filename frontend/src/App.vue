<template>
  <div id="app">
    <login v-if="state === 'auth'"/>
    <goods-list v-if="state === 'list'"/>
  </div>
</template>
<script>
import axios from 'axios'

import Login from './components/Login'
import GoodsList from './components/GoodsList'

export default {
    name: 'app',

    components: {Login, GoodsList},

    data () {
        return {
            state: 'auth'
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
    }
}
</script>

