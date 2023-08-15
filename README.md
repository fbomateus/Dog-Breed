# Dog Breed Project

## Installation

### System Requirements

You need to have this installed to configure this project locally:

- [Composer](https://getcomposer.org/)
- [Docker](https://www.docker.com/)
- [Lando](https://docs.lando.dev/)

### Local configuration

After install Lando follow these steps:

- Build and start containers in your local environment.
```sh
> lando start
```

- After starting lando you need to install composer.
```sh
> lando composer install
```

Access website as administrator:

- Generate url with drush.
```sh
> lando drush uli
```

- Login via '/user' route.
```sh
> user: admin, password: erV5aUaSc4
```
