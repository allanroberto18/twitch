<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 mt-4 mb-4">
                <h2>
                    Most Popular Channels: <button type="button" v-on:click="getChannels" class="btn btn-link">Refresh Channels</button>
                </h2>
                <div class="media mt-4">
                    <ul class="list-unstyled">
                        <li class="media mb-4" v-for="item in channels.data">
                            <img class="mr-3"
                                 v-bind="{
                                src: item.thumbnail_url.replace('{width}x{height}', '140x80'),
                                title: item.title,
                                alt: item.title
                             }"
                            >
                            <div class="media-body">
                                <h5 class="mt-0 mb-1">{{ item.user_name }} | {{ item.type }}</h5>
                                {{ item.title }} | <strong>{{ item.viewer_count }} viewers</strong><br />
                                <a v-on:click="embedStream(item.user_name)" class="mt-1 btn btn-primary btn-sm text-white">
                                    Play
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card mt-4 mb-4">
                    <div class="card-header text-white bg-primary">Choose your favorite stream </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <input type="text" v-model="channel" class="form-control"
                                       placeholder="insert your favorite streamer"
                                       aria-label="insert your favorite streamer"
                                       aria-describedby="choose your channel">
                                <div class="input-group-append">
                                    <button class="btn btn-primary text-white btn-outline-secondary"
                                            v-on:click="embedStream(channel)"
                                            v-scroll-to="'#embed'"
                                            type="button">
                                        Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="embed-responsive embed-responsive-16by9 mt-4">
                    <div id="embed" class="embed-responsive-item"></div>
                </div>
                <div v-if="events.length > 0" class="mt-4">
                    <h3>Events</h3>
                    <p v-for="item in events">{{ item }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import Pusher from 'pusher-js';

    export default {
        data() {
            return {
                width: 800,
                height: 600,
                channel: '',
                channelId: '',
                channels: [],
                errors: [],
                token: '',
                events: [],
                pusher: new Pusher('c6b3bd9cccfffab444d0', {
                    cluster: 'eu',
                    forceTLS: true
                })
            }
        },

        mounted() {
            if (localStorage.channel) {
                this.setChannel();
            }

            this.token = this.getTokenFromUrl('access_token');
            this.getChannels();
            let embedStream = document.createElement('script');
            embedStream.setAttribute('src', 'https://embed.twitch.tv/embed/v1.js');
            document.head.appendChild(embedStream);
        },

        methods: {
            setChannel() {
                this.channel = localStorage.channel;
            },

            getTokenFromUrl(key) {
                let matches = this.$route.hash.match(new RegExp(key + '=([^&]*)'));
                return matches ? matches[1] : null;
            },

            getChannels() {
                this.channels = [];
                const url = './api/streams/popular';
                axios
                    .get(url)
                    .then(response => {
                        this.channels = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    })
                ;
            },

            embedStream(username = '') {
                this.channelId = '';
                this.events = [];
                document.getElementById("embed").innerHTML = "";
                let embed = {};
                if (username == '') {
                    return ;
                }

                const url = './api/streams/user?channel=' + username + '&token=' + this.token;
                axios
                    .get(url)
                    .then(response => {
                        const data = response.data.data[0];

                        this.channel = data.user_name;
                        this.channelId = data.user_id;
                        this.events = [];
                        if (this.channel !== '') {
                            const streamDetail = data.user_name + ': ' +
                                data.title + ', ' +
                                data.viewer_count + ': viewers - language: ' +
                                data.language + ' - ' +
                                data.type
                            ;
                            this.events.unshift(streamDetail);
                            this.pusher.logToConsole = true;
                            this.pusher.subscribe(this.channelId);
                            this.pusher.bind('event_changed', data => {
                                console.log('Incoming via pusher..', data);

                                this.events.unshift(data.message);
                            });
                        }
                    })
                    .catch(error => {
                        this.channel = '';
                        this.channelId = '';
                        this.events = [];
                    })
                    .finally(() => {
                        if (this.channel == '') {
                            return;
                        }

                        embed = new Twitch.Embed("embed", {
                            width: '100%',
                            height: 'auto',
                            channel: this.channel,
                            layout: "video-with-chat",
                            autoplay: false
                        });

                        embed.addEventListener(Twitch.Embed.VIDEO_READY, () => {
                            let player = embed.getPlayer();
                            player.play();
                        });
                    })
                ;
            }
        }
    }
</script>
