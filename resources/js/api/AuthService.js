import Api from './Api'

export default {
    // getUser() {
    //     return Api().get(`basket`)
    // },
    postLogin(data) {
        return Api().post('login',data)
    },
    postRegister(data) {
        return Api().post(`register`,data)
    },

}
