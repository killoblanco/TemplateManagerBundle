# Symfony Template Manager Bundle

## Instalation

An important thing is that we use the symfony's serializer component. If you have it
already enabled in your config file you can pass by. Otherwise to enable it you can
uncommet the line in your own config file.

```YML
# app/config/config.yml
framework:
    # ...
    serializer: { enable_annotations: true }
```

## Usage

### Controlls

The controls are defined and guied followin the HTML 5 standars and so including
basic bootstrap implementation.