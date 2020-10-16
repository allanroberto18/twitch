## About the project

[The application](https://alr-twitch.herokuapp.com/) was deployed on heroku  

**Frameworks used**
- [Laravel](https://laravel.com/docs/routing) to create API  
- [Guzzle](http://docs.guzzlephp.org/en/stable/) to consume APIs   
- [VueJS](https://vuejs.org/), [VueRouter](https://router.vuejs.org/) and [vue-scrollto](https://github.com/rigor789/vue-scrollto).  

**The goal of this project is:**
- Connect the [application](https://alr-twitch.herokuapp.com/) to [Twitch](https://dev.twitch.tv/)  
- Get streamer channels (some favorites) and by username  
- Get at **realtime** the streamer events  

**API endpoits**
- **GET**  - /api/streams/popular  
- **GET**  - /api/streams/user?channel={username}&token={access_token}  
- **GET**  - /api/auth/url   
- **GET**  - /api/callback/handler?hub.mode={mode}&hub.topic={topic}&hub.lease_seconds={lease_seconds}&hub.challenge={challenge}   
- **POST** - /api/callback/handler  
```json
{
    "data": [
        {
            "id": "1234",
            "login": "1234login",
            "display_name": "hiiam1234",
            "type": "staff",
            "broadcaster_type": "",
            "description": "1234 is me",
            "profile_image_url": "https://link/to/pic/1234.jpg",
            "offline_image_url": "https://link/to/offline_pic/1234_off.jpg",
            "view_count": 3455
         }
     ]
}
``` 

**How everything works**
- The frontend will request by VueJS an authentication url from local api  
- After authentication will be display the 10 favorite streamers to select or looking for another channel  

**Deployment AWS Scenario**  
Such kind of application small application, to deploy is necessary configure:  
- **EC2:** linux instance to config PHP/Nginx or PHP/Apache2  
- **Route53:** for managing DNS  

**But for improve is necessary**  
- **S3 and Cloudfront CDN:** hosting static content and cache  
- **AWS Auto Scaling and Load Balancing:** to manage the request and create new instances when is necessary  
