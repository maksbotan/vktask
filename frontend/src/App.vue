<template>
  <div id="app">
    <login v-if="state === 'auth'" @logged-in="state = 'list'"/>
    <goods-list v-if="state === 'list'"
                @clicked="select"
                @edit="(goodID) => { selectedGoodID = goodID; state = 'add-or-edit'}"
                @create-new="selectedGoodID = null; state='add-or-edit'"
    />
    <good v-if="state === 'good'"
          :goodID="selectedGoodID"
          @go-back="state = 'list'; selectedGoodID = null"
          @edit="state = 'add-or-edit'"
    />
    <add-or-edit v-if="state === 'add-or-edit'"
                 :existingGoodID="selectedGoodID"
                 @go-back="state = 'list'; selectedGoodID = null"
                 @done="editingDone"
    />
  </div>
</template>
<script>
import axios from 'axios'

import Login from './components/Login'
import GoodsList from './components/GoodsList'
import Good from './components/Good'
import AddOrEdit from './components/AddOrEdit'

export default {
    name: 'app',

    components: {Login, GoodsList, Good, AddOrEdit},

    data () {
        return {
            state: 'auth',
            selectedGoodID: null
        }
    },

    created () {
        axios.interceptors.response.use(r => r, error => {
            if (error.response.status === 401) {
                // If unauthorized, reset page to login form
                // And pop token from local storage
                this.state = 'auth'
                localStorage.removeItem('vktask-auth-token')

                return Promise.reject('unauthorized')
            } else {
                console.log(error)
                return Promise.reject(error)
            }
        })
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
        },

        editingDone (goodID) {
            if (goodID) {
                this.selectedGoodID = goodID
                this.state = 'good'
            } else {
                this.selectedGoodID = null
                this.state = 'list'
            }
        }
    }
}
</script>

