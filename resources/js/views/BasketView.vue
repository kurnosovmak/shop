<template>
    <div>

        <center>
            <div style="width: 600px;">
                <table class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">Фото</th>
                        <th scope="col">Название</th>
                        <th scope="col">Количество</th>
                        <th scope="col">Цена</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="basketItem in basketItems" v-bind:key="basketItem.product.id">
                        <td ><img class="pre-image" :src="basketItem.product.image"></td>
                        <td><router-link :to="'/product/'+basketItem.product.id">{{basketItem.product.title}}</router-link></td>
                        <td><a v-on:click="addItem(basketItem.product.id,-1)" class="btn btn-primary">-</a>
                            {{basketItem.count}}
                            <a v-on:click="addItem(basketItem.product.id,1)" class="btn btn-primary">+</a></td>
                        <td>{{basketItem.product.price * basketItem.count}} руб.</td>
                    </tr>
                    <tr >
                        <td ></td>
                        <td></td>
                        <td></td>
                        <td>{{allPrice}} руб.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <a v-on:click="order" class="btn btn-primary">Оформить заказ</a>
        </center>


    </div>
</template>

<script>
import BasketService from "../api/BasketService";
import OrderService from "../api/OrderService";

export default {
    name: "ProductView",
    data: function () {
        return {
            basketItems: [],
            allPrice:0,
        };
    },
    methods: {
        async getBasket() {

            const basket = await BasketService.getBasket().catch((err) => {
                console.log(err)
            }).finally(() => {

            })
            if (typeof basket === 'undefined') return

            if (basket.data.data.length) {
                this.basketItems = basket.data.data;
                this.getAllPrice();
            } else {

            }
        },

        async addItem(product_id,countAdd) {


            const basket = await BasketService.putBaketProductById(1, {
                product_id: product_id,
                count: countAdd
            }).catch((err) => {
                console.log(err)

            })
                .finally(() => {

                })
            if (typeof basket === 'undefined') return

            if (basket.data.status = 1) {
                this.getBasket();


            } else {

            }
        },

        async order(){
            if (this.basketItems.length <=0){
                this.flash('Оформить заказ с пустой корзиной не возможно', 'error', {
                    timeout: 3000,
                });
                return;
            }
            const order = await OrderService.order({
                contact:'user'
            }).catch((err) => {
                console.log(err)
            }).finally(() => {

            })
            if (typeof order === 'undefined') return

            if (order.data.status = '1') {
                this.basketItems = [];
                this.getAllPrice();
                this.flash('Заказ оформлен', 'success', {
                    timeout: 3000,
                });
            } else {
                this.flash('Ошибка при оформлении заказа', 'error', {
                    timeout: 3000,
                });
            }
        },

        getAllPrice(){
            this.allPrice =0;
            for(var i=0;i<this.basketItems.length;i++){
                this.allPrice += this.basketItems[i].product.price * this.basketItems[i].count;
            }
        },
    },
    mounted() {
        this.getBasket();
        this.getAllPrice();
    }

}
</script>

<style >


.pre-image{
    width: 50px;
}
</style>
