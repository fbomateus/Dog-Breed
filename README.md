# Dog Breed Project

## Installation

### System Requirements

You need to have this installed to configure this project locally:

- [Composer](https://getcomposer.org/)
- [Docker](https://www.docker.com/)
- [Lando](https://docs.lando.dev/)

### Local configuration

After install Lando follow these steps:

- Install Composer dependencies.
```sh
> composer install
```

- Build and start containers in your local environment.
```sh
> lando start
```

- Import database.
```sh
> lando db-import db/dump.sql.gz
```

- Clear cache.
```sh
> lando drush cr
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
