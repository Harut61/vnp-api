# Env variable List

List of all Params JSON File [Click Here](https://gitlab.com/ivnews/vnp-api/-/blob/develop/variable.json) 

Hi! Here is a list of all env variable required in **VNP API**
| Variable Name | Value | Desc. | Required
|--|--|--|--|
| APP_ENV | dev | application environment  | **YES** |
| APP_NAME | ivn | Reflect prefix for **Buckets** **sqs** ex. **ivn**-local-img  | **YES** |
| APP_SECRET | string  | (Recommended Length 32 characters) In practice, Symfony uses this value for encrypting the cookies used in the remember me functionality [Click Here](https://symfony.com/doc/current/reference/configuration/framework.html#secret) | **YES** |
| TRUSTED_PROXIES | 127.0.0.1,REMOTE_ADDR |  | **NO** |
| TRUSTED_HOSTS |  |  | **NO**|
| DATABASE_URL |  |  | **YES** |
| CORS_ALLOW_ORIGIN |  |  | **YES** |
| JWT_SECRET_KEY | %kernel.project_dir%/config/jwt/private.pem |  | **YES** |
| JWT_PUBLIC_KEY | %kernel.project_dir%/config/jwt/public.pem |  | **YES** |
| JWT_PASSPHRASE |  | empty always | **YES** |
| SENTRY_DSN | url | sentry url | **YES** |
| MAILER_URL | url | smtp url | **YES** |
| FROM_EMAIL | email | sender email | **YES** |
| API_ENTRYPOINT | https://vnp-api.dev.ivnews.com | Api Env Specific Url | **YES** |
| UUI_ENTRYPOINT | https://vnp-uui.dev.ivnews.com | Universal UI Env Specific Url | **YES** |
| IMG_CDN_ENTRYPOINT | url |  | **YES** |
| REDIS_URL | url | redis url | **YES** |
| B2_ENTRYPOINT | https://s3.us-west-002.backblazeb2.com |  | **YES** |
| B2_REGION | us-west-002 |  | **YES** |
| B2_ACCESS_KEY | access key |  | **YES** |
| B2_SECRET_KEY | secret key |  | **YES** |
| B2_VOD_UPLOAD_BUCKET | vod-transcoder | here we add bucket name without prefix of **APP_NAME** and **APP_ENV** ivn-local- | **YES** |
| B2_ACL | private | private or public depends upon type | **YES** |
| PRE_SIGNED_EXP_TIME | int | 240 presign url expire | **YES** |
| AWS_SQS_ACCESS_KEY | string | sqs access key | **YES** |
| AWS_SQS_SECRET_KEY | string | sqs secret key | **YES** |
| AWS_SQS_REGION | string | aws region | **YES** |
| AWS_SDK_VERSION | 2012-11-05 | aws sdk version | **YES** |
| VOD_REDIS_KEY_PREFIX | vod:upload.ivnews.com: | store vod info while uploading with presign URL  | **YES** |
| AWS_TRANSCODING_SQS_URL | aws transcoding queue url |  | **YES** |

======= **Change Log** - **21-11-2020** ======= 

## Parameter Added

| Variable Name | Value | Desc. | Required
|--|--|--|--|
| AWS_MEDIA_INFO_SQS_QUEUE_NAME | vod-mediainfo | application environment  | **YES** |
| AWS_MEDIA_INFO_SQS_QUEUE_TYPE | standard | aws sqs type fifo or standard | **YES**
| AWS_TRANSCODING_SQS_QUEUE_NAME | vod-transcoder | application environment  | **YES** |
| AWS_TRANSCODING_SQS_QUEUE_TYPE | standard | aws sqs type fifo or standard | **YES**

## Parameter Removed


|| Variable Name ||  
|--|--|--|  
|| AWS_TRANSCODING_SQS_URL ||

======= **Change Log** - **25-11-2020** ======= 

## Parameter Added

| Variable Name | Value | Desc. | Required
|--|--|--|--|
| B2_MASTER_ACCESS_KEY | abcdefg | used for upload video  | **YES** |
| B2_MASTER_SECRET_KEY | standard | aws sqs type fifo or standard | **YES**
| ADMIN_ALERT_EMAIL | vnp-admin@ivnews.com | Send Video alerts on this email    | **YES** |

======= **Change Log** - **26-11-2020** ======= 
## Parameter Removed

Due to slash comes in AWS Credentials, the database and swift mailer URLs are not working.  we are replacing it with parameters.

|| Variable Name ||  
|--|--|--|  
|| MAILER_URL ||
|| DATABASE_URL ||

## Parameter Added
| Variable Name | Value | Desc. | Required
|--|--|--|--|
| SWIFT_TRANSPORT | smtp | mailer protocol  | **YES** |
| SWIFT_USERNAME | username | | **YES**
| SWIFT_PASSWORD | password | | **YES** 
| SWIFT_HOST | gmail.com |  | **YES** 
| SWIFT_PORT | 597 | smtp port | **YES** 
| SWIFT_ENCRYPTION | tls | tls or ssl    | **YES**
|--|--|--|--|
| DATABASE_HOST | localhost | mailer protocol  | **YES** |
| DATABASE_PORT | 3306 || **YES**
| DATABASE_NAME | ivnews_db || **YES** 
| DATABASE_USERNAME | root | | **YES** 
| DATABASE_PASSWORD | root | | **YES** 
| DATABASE_SERVER_VERSION | 5.7 | | **YES**



======= **Change Log** - **2O-12-2020** ======= 
## Parameter Added
| Variable Name | Value | Desc. | Required
|--|--|--|--|
| AWS_MEDIA_CONVERTER_VERSION | 2017-08-29 |   | **YES** |
| AWS_MEDIA_CONVERTER_REGION | us-east-1 | | **YES**
| AWS_MEDIA_CONVERTER_ACCESS_KEY | | | **YES** 
| AWS_MEDIA_CONVERTER_SECRET_KEY | |  | **YES** 
| AWS_MEDIA_CONVERTER_ENDPOINT | | | **YES** 
| AWS_MEDIA_CONVERTER_IAM |  | | **YES**
| AWS_MEDIA_CONVERTER_BILLING_TAG |  | | **YES**
|--|--|--|--|
| INTACKER_ACCESS_KEY |  | min.io  access key | **YES** |
| INTACKER_SECRET_KEY |  | min.io  secret key| **YES**
| INTACKER_ENDPOINT | min.io  url || **YES** 
| INTACKER_UPLOAD_BUCKET | vod-upload | | **YES**


======= **Change Log** - **30-12-2020** ======= 
## Parameter Added
| Variable Name | Value | Desc. | Required
|--|--|--|--|
| B2_VOD_BUCKET | vod | | **YES**
| AWS_S3_ACCESS_KEY | ***** | | **YES**
| AWS_S3_SECRET_KEY | ***** | | **YES**
| AWS_S3_TRANSCODING_OUTPUT_BUCKET | transcoding | | **YES**



======= **Change Log** - **04-01-2021** ======= 
## Parameter Added
| Variable Name | Value | Desc. | Required
|--|--|--|--|
| B2_CDN_URL | https://vod-cdn-b2.fitvnews.com | | **YES**
| VOD_CDN_URL | https://vod-cdn-b2.fitvnews.com | | **YES**

======= **Change Log** - **03-02-2021** ======= 
## Parameter Added

| IVN_ENV | local,dev,prod | According to our env this is used for specifying buckets sqs name like ivn-dev etc. | **YES**


======= **Change Log** - **10-03-2021** ======= 
## Parameter Added
| Variable Name | Value | Desc. | Required
|--|--|--|--|
| WASABI_ENTRYPOINT | https://vod-cdn-b2.fitvnews.com | | **YES**
| WASABI_REGION | us-east-1 | | **YES**
| WASABI_ACCESS_KEY | access_key | | **YES**
| WASABI_SECRET_KEY | secret | | **YES**
| VOD_BUCKET | vod | | **YES**

======= **Change Log** - **15-03-2021** ======= 
## Parameter Added
| Variable Name | Value | Desc. | Required
|--|--|--|--|
| AWS_S3_TRANSCODING_OUTPUT_BUCKET | transcoding | | **YES**
| SOURCE_VIDEO_CDN_URL | https://ivn-dev-transcoding.b-cdn.net | | **YES**



======= **Change Log** - **20-04-2021** ======= 
## Parameter Added
| Variable Name | Value | Desc. | Required
|--|--|--|--|
| GOOGLE_CLIENT_SECRET | secret-key | | **YES**
| GOOGLE_CLIENT_ID | ourkey.apps.googleusercontent.com | | **YES**


======= **Change Log** - **03-05-2021** ======= 
## Parameter Remove
| Variable Name | Value | Desc. | Required
|--|--|--|--|
| GOOGLE_CLIENT_ID | ourkey.apps.googleusercontent.com | | **YES**

======= **Change Log** - **03-05-2021** ======= 
## Parameter Added
| Variable Name | Value | Desc. | Required
|--|--|--|--|
| GOOGLE_CLIENT_ID_WEB | ourkey.apps.googleusercontent.com | | **YES**
| GOOGLE_CLIENT_ID_ANDROID | ourkey.apps.googleusercontent.com | | **YES**
| GOOGLE_CLIENT_ID_IOS | ourkey.apps.googleusercontent.com | | **YES**


======= **Change Log** - **02-08-2021** ======= 
## Parameter Added
| Variable Name | Value | Desc. | Required
|--|--|--|--|
| VNE_INTEGRATION_SQS_QUEUE_NAME | vnp-vne-integration | | **YES**


======= **Change Log** - **06-09-2021** ======= 
## Parameter Added
| Variable Name | Value | Desc. | Required
|--|--|--|--|
| VNE_AUDIO_LINK_SQS_QUEUE_NAME | vne-dev-audio-links.fifo | | **YES**
| VNE_HOST | vne-core.dev.ivnews.com:443 | | **YES**