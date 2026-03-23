## 2.25.0

* Support Symfony 8 and PHP 8.5.

### Routing configuration

The `routing.xml` file will be removed in the next major version. If you use Symfony 8, routing is only supported in PHP or
YAML format, but no longer in XML.

This only affects the routing file if you reference it explicitly; the bundle routes themselves are unchanged.

Update the resource path from `routing.xml` to `routing.yaml`:

```yaml
# Before
_monitor:
    resource: "@LiipMonitorBundle/Resources/config/routing.xml"

# After
_monitor:
    resource: "@LiipMonitorBundle/Resources/config/routing.yaml"
```

## Upgrade from 2.11 to 2.12

### [Possible BC BREAK] Switch to `laminas/laminas-diagnostics`

`zendframework/zenddiagnostics` has been deprecated and replaced with `laminas/laminas-diagnostics`. The API remains the same
but the namespace has changed (`ZendDiagnostics\*` to `Laminas\Diagnostics\*`). If using this bundle without custom reporters
or checks, there is no BC break. If using custom checks/reporters, you will need to update their imports:

```diff
- use ZendDiagnostics\*;
+ use Laminas\Diagnostics\*;
```
