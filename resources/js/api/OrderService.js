import Api from './Api'

export default {
    order(data) {
        return Api().post(`order`,data)
    },


}
