# Wall - API

## Description
PHP/Laravel RESTful API to manage users, posts, comments, registration, authorizaion. 

## Setup

### Requirements
* [Git Client](https://git-scm.com/downloads)
* [Vagrant](https://www.vagrantup.com/downloads.html)
* [Virtual Box](https://www.virtualbox.org/wiki/Downloads)

### Installation Steps

1. Clone this repo to your local environment:
   ```
   git clone https://github.com/diegocam/WallAPI.git
   ```
2. Move into the directory created above `cd WallAPI`
3. Run and install the Vagrant environment. This may take about 10-20 minutes depending on your machine 
   ```
   vagrant up —-provision
   ```
4. Copy `.env.example` to a new file called `.env`
   ```
   cp .env.example .env
   ```
5. Add an entry to your Hosts file (/etc/hosts). This is the IP/domain Vagrant is setup to use (192.168.50.5 wall-api.local)
   ```
   echo "192.168.50.5 wall-api.local" | sudo tee -a /etc/hosts
   ```
6. SSH into the vagrant environment 
   ```
   vagrant ssh
   ```
7. Install dependencies with composer 
   ```
   composer install
   ```
8. Set your application key. This should automatically add an encrypted key inside `.env` for the `APP_KEY=` entry
   ```
   php artisan key:generate
   ```
9. Set your DB env variables in `.env`
    ```
    DB_DATABASE=wall
    DB_USERNAME=vagrant
    DB_PASSWORD=vagrant
    ```
10. Run migrations: `php artisan migrate`
11. Set encryption keys for Passport:
    ```
    php artisan passport:keys
    ``` 
12. Create a password grant client. (**CRUCIAL**)
    1. Run `php artisan passport:client --password`.
    2. It will ask your for a name, you may hit enter to leave it as is or enter `Wall Front` for a more descriptive name.
    3. When done, you will see a `Client ID` and a `Client Secret`. You will need those to update your `.env` entries. 
    4. Open up `.env` and towards the bottom you will see the empty entries. Fill them out using the `Client ID` and `Client Secret` from above.
    ```
    PASSPORT_CLIENT_SECRET=
    PASSPORT_CLIENT_ID=
    ```
13. On a browser, try going to http://wall-api.local to see the Laravel welcome screen. If you see this, you have successfully installed the API locally.
