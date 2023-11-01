# Welcome to VNP API!

Hi! **VNP API** is build with  [API Platform](https://api-platform.com/) which is next-generation web framework designed to easily create API-first projects without compromising extensibility and flexibility

# Install
Install docker-compose version 1.29.2
```
sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
```

## Environment setup 
- Copy `.env.dist` to `.env.local`
- Update correct variables in `.env.local`
- Configure env file option like this https://docs.docker.com/compose/environment-variables/#the-env_file-configuration-option
- Start local development by using this command

```
docker-compose up -d
```

- To start docker compose with rebuilding the images using this command

```
docker-compose up -d --build
```

####  Update **APP_ENV** in **.env**  file according environment

    APP_ENV=prod


while setting up environment initially you need to run following commands

    composer install
    composer run setup

#### composer install
it will install project dependency to vendor directory
#### composer run setup
this command will run following command 

    chmod -R 777 config/jwt
	mkdir -p config/jwt  
	bin/console doctrine:database:create --if-not-exists
	bin/console doctrine:schema:update -f
	bin/console doctrine:migrations:sync-metadata-storage 
	bin/console doctrine:migrations:migrate --no-interaction
	bin/console ivnews:paas-setup
	bin/console lexik:jwt:generate-keypair

	# openssl genrsa -out config/jwt/private.pem 4096
	# openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem


## Update enviroment

 while updating any enviroment to deploy updated code and it's dependencies.  you need to run following commands

    composer install
    composer run deploy
    
## VNE Generate Proto

    docker run -v `pwd`:/defs namely/protoc-all -f events.proto -l php -o src/VneUtil

## Worker Commands

 if you want to run mediainfo and transcoding worker
 ### vod Media Info
 php bin/console ivnews:aws-sqs-worker --queue=vod-mediainfo
 ### vod transcoding
 php bin/console ivnews:aws-sqs-worker --queue=vod-transcoder
 ### delete source Video
 php bin/console ivnews:aws-sqs-worker --queue=source-video-delete
 ### vnp vne integration
 php bin/console ivnews:aws-sqs-worker --queue=vnp-vne-integration
 ### vnp vne bulk sync
 php bin/console ivnews:aws-sqs-worker --queue=vnp-vne-bulk-sync
 
