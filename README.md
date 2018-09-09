```
                            88                                     
                            88                                     
                            88                                     
                            88  ,adPPYba,  ,adPPYb,d8  ,adPPYba,   
                            88 a8P_____88 a8"    `Y88 a8"     "8a  
                            88 8PP""""""" 8b       88 8b       d8  
                            88 "8b,   ,aa "8a,   ,d88 "8a,   ,a8"  
                            88  `"Ybbd8"'  `"YbbdP"Y8  `"YbbdP"'   
                                           aa,    ,88              
                                            "Y8bbdP"        
                            -------------------------------------
                                  Light - Easy - Good - Open 


```
Work directly WITH USER LOGIN :-D
                
SF 4.1 and more

IDK LEGO BUNDLE V 0.1 alpha (do not use it in production)

You want an very customable without to see internal code, Stay here !
There is nothing you can't to do.

Build your pages simply by adding configurable components.
Add a filter, add a list, add a form then go

1: composer create-project symfony/website-skeleton empty
2: In framework.yml:
```yaml
freamworks:
  default_locale: fr
  translator:
      fallbacks: ['fr']
  templating:
      engines: ['twig']
```
3: add in composer.json
```json
"repositories": [
        {
            "url": "https://github.com/prestigejo/lego-bundle.git",
            "type": "git"
        }

    ],
    "require": {
        "prestigejo/legobundle": "dev-master",
    }
```
4: If do not use FOSuser create App\Entity\User and in security.yml 
```yaml
security:
    providers:
        main:
            entity: { class: App\Entity\User, property: username }
    encoders:
        App\Entity\User: { id: lego.security.password_encoder}

    firewalls:
        ...
        login:
            pattern: ^/login$
            security: false
        main:
            pattern: ^/
            anonymous: ~
            form_login:
                login_path: idk_lego_security_login
                check_path: idk_lego_security_check
                default_target_path: idk_lego_dashboard
            logout:
                path: /logout
                target: /login
            remember_me:
                secret: "secret"
                lifetime: 2232000

    access_control:
            - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/, role: ROLE_USER }
```
4 bis: With fos_user (or another):
```yaml
idk_lego:
    route_login: fos_user_security_check
```  
5: create user 
```  
php bin/console idk:user:create
```  
or (fosUser)
```  
php bin/console fos:user:create 
```  
7: Create lego page
```  
php bin/console make:lego 
```  
8: composer require server

9: bin/console server:run 0.0.0.0:8000


 ===> For Symfony 4.X

TODO 
- order menu item
- order item in itemList (XHR)
CURRENT v alpha 0.1.2

- Log history [ ] other bundle
- Media [ ] other bundle
- Mail [ ] other bundle
- Import [ ] other bundle
- Pagination [X]
- Field-route [X]
- Onglet component [ ]
- Default LayoutBase [X]
- Custom Bulk Action [X]
- Ressource Action [X]
- Menu auto [X]
- Right Bar [ ]
- Sub-Filter and Xhr filter [X]
- Upload File [X]
- EntityAction and ListAction auto [X]
- Rupteur [X]
- Group [ ]
- Form auto [X]
- Call template (ViewParams)[X] all object pass to the view is a ViewParams
- Url object (LegoPath) [X] all the url have to do with LegoPath
- Header and Menu object [X]
- Multi same component (CustomComponent or Multi ActionComponent) [X]
- Skeleton [X] //  SF4 maker [X]
- Reload line with item action [X]
- Move component [X]
- Dashboard [X]
- Widget Systeme [X]
- Check double bindRequest in subComponent [ ]
- ExportField [X]
- Flex [X]
- Doc [ ]
- Roles [X]
- Tree show [X]

v beta

- work without fosuser (optional) [X]
- Gestion ROLE [X]

Symfony 4

-require translator
```yaml
default_locale: fr
    translator:
       fallbacks: ['fr']
```

Next todo:

Opti menu OK
custom routeprefix OK
double config global OK
export by component OK
querybuilder filter list OK
connect or not OK
Check all type Form and branch dateTimeform
(Sub-)Filter OK, 
Multi-widget xhr-widget, Group bs, fos group, Custom action, itemAction and bulkAction

Optimisation todo:

 Use voter ,Filter, Tree, Action, tbody imbrique (breaker), Upload file+, getValue et setValue de Field, refactoring getType de Configurator, style tree, try gridstack, widget config

Bug todo:

- sort component is sort execution component !

Your config.yaml

```yaml
idk_lego:
    skin: skin-yellow
    layout: ::lego.html.twig
    service_menu_class: App\Service\Menu
    service_header_class: App\Service\Header
    service_footer_class: App\Service\Footer
```

