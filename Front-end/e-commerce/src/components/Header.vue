<template>
  <header id="header">
    <!--header-->
    <div class="header_top">
      <!--header_top-->
      <div class="container">
        <div class="row">
          <div class="col-sm-6">
            <div class="contactinfo">
              <ul class="nav nav-pills">
                <li>
                  <a href="#"><i class="fa fa-phone"></i> +2 95 01 88 821</a>
                </li>
                <li>
                  <a href="#"><i class="fa fa-envelope"></i> info@domain.com</a>
                </li>
              </ul>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="social-icons pull-right">
              <ul class="nav navbar-nav">
                <li>
                  <a href="#"><i class="fa fa-facebook"></i></a>
                </li>
                <li>
                  <a href="#"><i class="fa fa-twitter"></i></a>
                </li>
                <li>
                  <a href="#"><i class="fa fa-linkedin"></i></a>
                </li>
                <li>
                  <a href="#"><i class="fa fa-dribbble"></i></a>
                </li>
                <li>
                  <a href="#"><i class="fa fa-google-plus"></i></a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/header_top-->

    <div class="header-middle">
      <!--header-middle-->
      <div class="container">
        <div class="row">
          <div class="col-sm-4">
            <div class="logo pull-left">
              <router-link to="/"
                ><img v-bind:src="`${publicPath}images/home/logo.png`" alt=""
              /></router-link>
            </div>
          </div>
          <div class="col-sm-8">
            <div class="shop-menu pull-right">
              <ul class="nav navbar-nav">
                <li>
                  <router-link to="/account" v-if="user !== null"
                    ><i class="fa fa-user"></i> Account</router-link
                  >
                </li>
                <li v-if="user !== null">
                  <router-link to="/wishlist"
                    ><i class="fa fa-star"></i> Wishlist
                    <span class="badge">{{ wishCount }}</span></router-link
                  >
                </li>
                <li v-if="user !== null">
                  <router-link to="/checkout"
                    ><i class="fa fa-crosshairs"></i> Checkout</router-link
                  >
                </li>
                <li>
                  <router-link to="/cart"
                    ><i class="fa fa-shopping-cart"></i>
                    <span
                      >Cart <span class="badge">{{ cartCount }}</span></span
                    >
                  </router-link>
                </li>
                <li v-if="user !== null">
                  <a href="javascript:void(0)" v-on:click="logout()"
                    ><i class="fa fa-lock"></i> Logout</a
                  >
                </li>
                <li v-else>
                  <router-link to="/login"
                    ><i class="fa fa-lock"></i> Login
                  </router-link>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/header-middle-->

    <div class="header-bottom">
      <!--header-bottom-->
      <div class="container">
        <div class="row">
          <div class="col-sm-9">
            <div class="navbar-header">
              <button
                type="button"
                class="navbar-toggle"
                data-toggle="collapse"
                data-target=".navbar-collapse"
              >
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
            </div>
            <div class="mainmenu pull-left">
              <ul class="nav navbar-nav collapse navbar-collapse">
                <li><router-link to="/" class="">Home</router-link></li>
                <li class="dropdown">
                  <a href="#">Categories<i class="fa fa-angle-down"></i></a>
                  <ul role="menu" class="sub-menu">
                    <li v-for="(category, index) in categories" v-bind:key="index">
                      <router-link
                        v-bind:to="{
                          name: 'Category',
                          params: { id: category.id },
                        }"
                        >{{ category.name }}</router-link
                      >
                    </li>
                  </ul>
                </li>
                <li class="dropdown">
                  <a href="#">Blog<i class="fa fa-angle-down"></i></a>
                  <ul role="menu" class="sub-menu">
                    <li v-for="(blog, index) in cms" v-bind:key="index">
                      <router-link
                        v-bind:to="{
                          name: 'CMS',
                          params: { slug: blog.slug },
                        }"
                        >{{ blog.title }}</router-link
                      >
                    </li>
                  </ul>
                </li>
                <li class="dropdown" v-if="user !== null">
                  <a href="javascript:void(0)"
                    >Orders<i class="fa fa-angle-down"></i
                  ></a>
                  <ul role="menu" class="sub-menu">
                    <li><router-link to="/myorders">My Orders</router-link></li>
                    <li>
                      <router-link to="/trackorders">Track Orders</router-link>
                    </li>
                  </ul>
                </li>
                <li><router-link to="/contact">Contact</router-link></li>
              </ul>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="search_box pull-right">
              <input type="text" placeholder="Search" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/header-bottom-->
  </header>
  <!--/header-->
</template>

<script>
// import { mapState } from "vuex";
import { categories, cmss, userLogout } from "../common/Service";
import store from "../store";
export default {
  name: "Header",
  data() {
    return {
      publicPath: process.env.BASE_URL,
      categories: [],
      cms: [],
    };
  },
  methods: {
    logout() {
      userLogout().then((res) => {
        console.log(res.data);
      });
      localStorage.removeItem("user");
      store.dispatch("user", null);
      this.$router.push("/login");
    },
  },
  mounted() {
    cmss().then((res) => {
      this.cms = res.data.cms;
    });
    categories().then((res) => {
      this.categories = res.data.categories;
    });
  },
  computed: {
    user() {
      return this.$store.getters.user;
    },
    cart() {
      return this.$store.getters.cart;
    },
    cartCount() {
      if (this.$store.getters.cart.length === 0) {
        return null;
      } else {
        return this.$store.getters.cart.length;
      }
    },
    wishCount() {
      if (
        this.$store.getters.wishlist.length === 0 ||
        this.$store.getters.wishlist === null
      ) {
        return null;
      } else {
        return this.$store.getters.wishlist.length;
      }
    },
  },
  // email: (state) => state.email,
};
</script>

<style>
.badge {
  background-color: #fe980f;
}
</style>