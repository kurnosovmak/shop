<template>
    <div id="register">
        <h1>Register</h1>
        <masked-input @input="input.phone = '8'+arguments[1]" mask="\+\7 (111) 111 11-11" placeholder="Phone"/>
        <input type="password" name="password" v-model="input.password" placeholder="Password"/>
        <button type="button" v-on:click="login()">Login</button>
    </div>
</template>

<script>
import MaskedInput from 'vue-masked-input'
import AuthService from "../api/AuthService";

export default {
    name: "RegisterView",
    data() {
        return {
            input: {
                phone: "",
                password: ""
            }
        }
    },
    methods: {
        async login() {
            if (this.input.phone == "" || this.input.password == "") {
                this.flash('Поля не должны быть пустыми', 'error', {
                    timeout: 3000,
                });
                return;
            }
            const login = await AuthService.postRegister({
                phone: this.input.phone,
                password: this.input.password
            }).catch((err) => {
                console.log(err)

            }).finally(() => {

            })
            if (typeof login === 'undefined') return
            // console.log(this.input.phone    )
            console.log(login.data)
            if (login.data.status == '1') {
                localStorage.setItem('token', login.data.token)

                this.$router.push('/')
            } else {
                this.flash('Не верные данные ', 'error', {
                    timeout: 3000,
                });
            }
        }
    },
    components: {
        MaskedInput
    }
}

</script>

<style scoped>

</style>
