import Api from './Api'

export default {
    getBasket() {
        return Api().get(`basket`,)
    },
    putBaketProductById(id, data) {
        return Api().put(`basket/${id}`, data)
    },
    clearBasketById(id) {
        return Api().delete(`basket/${id}`)
    },

}
