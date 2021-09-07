<template>
    <div>

        <div style="width: 300px;height: 100%;display: inline-block;float: left;padding-top: 65px;">
            <input placeholder="Поиск" v-on:input="searchTextUpdate" style="width: 250px;display: block;margin: auto;"
                   type="text" v-model="searchModel">
            <br>
            <div
                style="overflow-y: scroll;height: 600px;margin-left: 25px;width: calc(100% - 50px);border: #eaeaea 1px solid;">
                <div v-for="category in categories" v-bind:key="category.id" style="margin: auto;">
                    <input type="checkbox" v-on:change="categoryUpdate" v-bind:value="category" v-model="categoriesSelect">
                    <label>{{ category.type }}</label><br>
                </div>
            </div>
        </div>
        <div
            style="padding: 50px;margin: auto;width: calc(100% - 300px);display: inline-block;box-sizing: border-box;float: right;">
            <card-component v-for="product in products" v-bind:key="product.id" :product="product"
                            :countProductInBasket="basketByIdProduct[product.id]!=null?basketByIdProduct[product.id].count:0"></card-component>
        </div>
    </div>
</template>

<script>
import ProductService from '../api/ProductService.js'
import CardComponent from "../components/CardComponent";
import BasketService from "../api/BasketService";
import CategoryService from "../api/CategoryService";

export default {
    name: "ProductsView",
    components: {CardComponent},
    created() {
        window.addEventListener('scroll', this.handleScroll);
    },
    destroyed() {
        window.removeEventListener('scroll', this.handleScroll);
    },
    data: function () {
        return {
            searchModel: '',
            loading: false,
            loaded: false,
            errored: false,
            products: [],
            categories: [],
            categoriesSelect: [],
            page: 1,
            basketByIdProduct: [],
            searchTextLendth: 0
        }
    },
    methods: {
        handleScroll(event) {
            let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;
            // console.log(bottomOfWindow)
            if (bottomOfWindow) {
                this.getProducts();
            }
        },
        searchTextUpdate() {


            if (this.searchModel.length >= 3) {
                this.products = [];
                this.page = 1;
                this.getProducts();
            } else if (this.searchModel.length < 3 && this.searchTextLendth >= 3) {

                this.page = 1;
                this.products = [];
                this.getProducts();
            }

            this.searchTextLendth = this.searchModel.length;
        },

        categoryUpdate() {

            this.page = 1;
            this.products = [];
            this.getProducts();

        },

        async getProducts() {
            if (!this.loaded) {
                this.loading = true
            }
            var data = {
                page: this.page,
            };
            if (this.searchModel.length >= 3) {
                data.search = this.searchModel;
            }

            if (this.categoriesSelect.length >= 1) {
                var ids = [];
                for (var i=0;i<this.categoriesSelect.length;i++){
                    ids.push(this.categoriesSelect[i].id);
                }
                data.categories = ids;
            }

            const products = await ProductService.getAll({params: data})
                .catch((err) => {
                    console.log(err)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
            if (typeof products === 'undefined') return

            if (products.data.data.length > 0) {
                if (products.data['last_page'] < this.page) return
                this.page += 1
                this.products.push(...products.data.data)
                this.loaded = true


            } else {

            }
        },

        async getCategories() {

            const categories = await CategoryService.getAll()
                .catch((err) => {
                    console.log(err)
                })
                .finally(() => {

                })
            if (typeof categories === 'undefined') return

            if (categories.data.data.length > 0) {

                this.categories = categories.data.data

            } else {

            }
        },

        async getBasketByIdProduct() {
            const basket = await BasketService.getBasket().catch((err) => {
                console.log(err)
            }).finally(() => {

            })
            if (typeof basket === 'undefined') return

            if (basket.data.data.length) {
                this.basketByIdProduct = [];
                for (var i = 0; i < basket.data.data.length; i++) {

                    this.basketByIdProduct[basket.data.data[i].product.id] = basket.data.data[i];

                }
            } else {

            }
            this.getProducts();
        }
    },
    mounted() {
        this.getBasketByIdProduct();
        this.getCategories();

    }
}
</script>

<style scoped>

</style>
