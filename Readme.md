# Deploying changes to the live server #

Deployment are automated to decrease the possible downtime. Files to run:

`deploy-theme-only.sh` - deploy changes to templates, layouts, CSS, DI etc. without installing new modules, upgrading
them or changing modules sequence.

`deploy-full.sh` - deploy changes that include installing new modules or data/schema upgrades.

Deployment process flow implemented in the above files:

1) go to the build system located in `/path/to/the/build/system/`;
2) pull changes, install modules, compile code and assets;
3) go to the live system in `/path/to/the/production/system/`;
4) enter maintenance mode (only for `deploy-full.sh`);
5) pull changes, run `composer install` (only for `deploy-full.sh`) and `setup:upgrade`;
6) copy generated files from the build system;
7) switch to the production mode;
8) turn off maintenance (only for `deploy-full.sh`).
