<template>
    <div>
        <h2>Please log in:</h2>
        <p>Username: <input type="text" v-model="username"/></p>
        <p>Password: <input type="password" v-model="password"/></p>

        <p v-if="error">Login error occured!</p>
        <p><button type="button" @click.prevent="login">Login</button></p>
    </div>
</template>
<script>
import axios from 'axios'

export default {
    name: 'login',

    data () {
        return {
            username: '',
            password: '',
            error: null
        }
    },

    methods: {
        async login () {
            if (!this.username || !this.password) {
                return
            }

            let result
            try {
                const { data } = await axios.post('/api/login', {
                    username: this.username,
                    password: this.password
                })
                result = data
            } catch (err) {
                console.log(err)
                this.error = true
                return
            }

            const token = result.token
            axios.defaults.headers.common['Authorization'] = 'Token ' + token;
            localStorage.setItem('vktask-auth-token', token)

            this.$emit('logged-in')
        }
    }
}

</script>
