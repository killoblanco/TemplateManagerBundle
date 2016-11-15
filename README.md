# Symfony Template Manager Bundle

## Instalation

To install it just add the followin line into your AppKernel file and run composer update

```PHP
# app/AppKernel.php

...
new killoblanco\TemplateManagerBundle\TemplateManagerBundle(),
...    
```

Once the AppKernel file is edited we need to get able the bundle's routes, so adding this on your routing.yml file you can do it.

```YML
...
template_manager:
    resource: "@TemplateManagerBundle/controller/"
    type:     annotation
    prefix: /
...
```

An important thing is that we use the symfony's serializer component. If you have it
already enabled in your config file you can pass by. Otherwise to enable it you can
uncommet the line in your own config file.

```YML
# app/config/config.yml
framework:
    # ...
    serializer: { enable_annotations: true }
```

Finally you only need to update your database by running the Symfony database update command
```SHELL
php app/console doctrine:schema:update --force
```

## Usage

### Controlls

The controls are defined and guied followin the HTML 5 standars and so including
basic bootstrap implementation.
