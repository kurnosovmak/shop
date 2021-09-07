<template>
    <div >

        <CardComponent :countProductInBasket="count" style="margin: auto;float: none;display: block;" v-if="product!=null&&count!=null"
                       :product="product"></CardComponent>


    </div>
</template>

<script>
import ProductService from "../api/ProductService";
import CardComponent from "../components/CardComponent";
import BasketService from "../api/BasketService";

export default {
    name: "ProductView",
    components: {CardComponent},
    data: function () {
        return {
            product: null,
            count: null,
        };
    },
    methods: {
        async getProduct() {
            const products = await ProductService.getById(this.$route.params.id).catch((err) => {
                console.log(err)
            }).finally(() => {

            })
            if (typeof products === 'undefined') return

            if (products.data.data) {

                this.product = products.data.data;
                await this.getCountProductInBasket();
                console.log(this.product!=null)
                console.log(this.count!=null)

            } else {

            }
        },

        async getCountProductInBasket() {
            const basket = await BasketService.getBasket().catch((err) => {
                console.log(err)
            }).finally(() => {

            })
            if (typeof basket === 'undefined') return
            this.count = 0;
            if (basket.data.data.length) {
                for (var i = 0; i < basket.data.data.length; i++) {

                    if (basket.data.data[i].product.id == this.product.id) {

                        this.count = basket.data.data[i].count;
                        console.log(this.count);
                    }
                }
            } else {

            }
        },
    },
    mounted() {
        this.getProduct();

    }

}
</script>

<style scoped>

</style>
