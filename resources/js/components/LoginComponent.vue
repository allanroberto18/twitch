<template>
    <section class="mt-4 mb-4">
        <div class="row justify-content-center">
            <div class="text-muted text-center mb-3">
                <img slot="icon" v-on:click="getAuthUrl" class="img-thumbnail" src="../../images/icon.png" width="200" />
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </section>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                data: null,
                loading: true,
                error: false
            }
        },
        created() {
            this.getAuthUrl()
        },
        watch : {
            '$route': 'getAuthUrl'
        },
        methods: {
            getAuthUrl() {
                const url = 'https://alr-twitch.herokuapp.com/api/auth/url';
                axios
                    .get(url)
                    .then(response => {
                        this.data = response.data;
                    })
                    .catch(error => {
                        this.error = true;

                        console.log('error: ', error);
                    })
                    .finally(() => {
                        this.loading = false;
                    })
                ;
            }
        }
    }
</script>
