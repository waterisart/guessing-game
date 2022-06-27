# Guessing Game

"Guess the Number” Game. A working project where the Server Sent Events is used to update front end in real time. Consists of backend based on symfony 6 and front end based on jquery+vanilla js, compiled with symfony encore.

## Getting Started
1. Please refer to the [symfony docker readme](README.symfony-docker.md) on how to use docker in this project.
2. If not already done [install nodejs and npm](https://www.digitalocean.com/community/tutorials/how-to-install-node-js-on-ubuntu-20-04) 
3. build the images and start the [docker](README.symfony-docker.md)
4. run `npm run dev` to compile javascript and other assets
5. run `npm run watch` if you plan to change code
6. You may find it very useful to peak into DB, you can do so by connecting locally to [DATABASE_URL in .env](.env) since port 5432 is exposed

## Registering
You will need to rigister at least one player in order to play the guessing game, but it is more fun with 3 players. Here is the registration page. ![page to register](https://i.imgur.com/2AEAABH.png )



## Demo
[![3 Minute Demo of the Number Guessing Game](https://i.imgur.com/NEvT0tU.png)](https://youtu.be/PH3uPv9l_3E "3 Minute Demo")


## Troubleshooting
You may need to run change ownership command inside your project dir, since folder /db will most likely change owner to root. Here is how: `sudo chown -R [username]:[username]`
Replace [username] with the username on your machine.