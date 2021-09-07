import Api from './Api'

export default {
  getAll(data) {
      // console.log(data)
    return Api().get(`product`,data)
  },
  getById(id) {
    return Api().get(`product/${id}`)
  },

}
