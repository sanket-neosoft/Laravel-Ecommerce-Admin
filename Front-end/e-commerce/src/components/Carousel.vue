<template>
  <section id="slider">
    <!--slider-->
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <div id="slider-carousel" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
              <li
                data-target="#slider-carousel"
                data-slide-to="0"
                class="active"
              ></li>
              <li
                v-for="(banner, index) in banners"
                v-bind:key="index"
                data-target="#slider-carousel"
                v-bind:data-slide-to="index + 1"
              ></li>
            </ol>

            <div class="carousel-inner">
              <div class="item active">
                <div class="col-sm-6">
                  <h1><span>E</span>-SHOPPER</h1>
                  <h2>{{ default_banner.caption }}</h2>
                  <p>
                    {{ default_banner.description }}
                  </p>
                  <router-link
                    v:bind-to="default_banner.link"
                    v-if="default_banner.link !== null"
                    class="btn btn-default get"
                  >
                    Get it now
                  </router-link>
                </div>
                <div class="col-sm-6">
                  <img
                    v-bind:src="MAIN_URL + default_banner.image"
                    class="girl img-responsive"
                    alt=""
                  />
                </div>
              </div>
              <div
                class="item"
                v-for="(banner, index) in banners"
                v-bind:key="index"
              >
                <div class="col-sm-6">
                  <h1><span>E</span>-SHOPPER</h1>
                  <h2>{{ banner.caption }}</h2>
                  <p>
                    {{ banner.description }}
                  </p>
                  <router-link
                    v:bind-to="default_banner.link"
                    v-if="banner.link !== null"
                    class="btn btn-default get"
                  >
                    Get it now
                  </router-link>
                </div>
                <div class="col-sm-6">
                  <img
                    v-bind:src="MAIN_URL + banner.image"
                    class="girl img-responsive"
                    alt=""
                  />
                </div>
              </div>
            </div>

            <a
              href="#slider-carousel"
              class="left control-carousel hidden-xs"
              data-slide="prev"
            >
              <i class="fa fa-angle-left"></i>
            </a>
            <a
              href="#slider-carousel"
              class="right control-carousel hidden-xs"
              data-slide="next"
            >
              <i class="fa fa-angle-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--/slider-->
</template>

<script>
import Vue from "vue";
import axios from "axios";
import VueAxios from "vue-axios";
import { banners } from "@/common/Service";
import MAIN_URL from "../common/Url";

Vue.use(VueAxios, axios);

export default {
  name: "Carousel",
  data() {
    return {
      banners: "",
      MAIN_URL: MAIN_URL,
      default_banner: "",
    };
  },
  mounted() {
    banners().then((res) => {
      this.banners = res.data.banners.slice(1);
      this.default_banner = res.data.banners[0];
    });
  },
};
</script>

<style>
</style>