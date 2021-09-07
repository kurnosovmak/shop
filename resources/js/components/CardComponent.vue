<template>
    <div class="card" style="width: 18em;">
        <img class="card-img-top" style="width:100%;" :src=" product.image " alt="Card image cap">
        <div class="card-body">
            <h5 class="card-title">{{ product.title }}</h5>
            <p class="card-text">{{ product.body }}</p>
            <router-link :to="'/product/'+product.id"><a class="btn btn-primary">Подробнее</a></router-link>

            <a v-on:click="addItem(-1)" class="btn btn-primary">-</a>
            {{ count }}
            <a v-on:click="addItem(1)" class="btn btn-primary">+</a>
        </div>
    </div>
</template>

<script>
import BasketService from "../api/BasketService";

export default {
    name: "CardComponent",
    data: function () {
        return {
            count: 0,
        };
    },
    methods: {
        async addItem(countAdd) {


            const basket = await BasketService.putBaketProductById(1, {
                product_id: this.product.id,
                count: countAdd
            }).catch((err) => {
                console.log(err)

            })
                .finally(() => {

                })
            if (typeof basket === 'undefined') return

            if (basket.data.status = 1) {
                this.count += countAdd;


            } else {

            }
        }

    },
    props: {
        countProductInBasket:{
            type: Number,
            require: true
        },
        product: {
            type: Object,
            require: true
        },
    },mounted() {
        this.count = this.countProductInBasket;
    }
}
</script>

<style scoped>
.card {
    /*border: #dddddd 1px solid;*/
    display: inline-block;
    background-color: #fbfbfb;
    border-radius: 10px;
    padding: 10px;
    margin: 15px 15px 15px 15px;
}
</style>
