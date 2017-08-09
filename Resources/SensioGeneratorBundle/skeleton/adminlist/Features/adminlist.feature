# language: fr
Fonctionnalité: Teste {{ entity_class |lower }}

Contexte:
Etant donné je suis connecte avec "admin" et "admin"
Etant donné je suis sur "admin/{{ entity_class |lower }}"

Scénario: Affichage liste
Alors le code de status de la réponse devrait être 200
Alors je devrais voir "Gestion des {{ label |lower }}"


Scénario: Creation
Etant donné je suis "add_new"
Alors le code de status de la réponse devrait être 200
{% for fieldName, data in fields %}
{% if fieldName|slice(0,4) == "date" %}
Etant donné je remplis "{{ entity_class |lower }}_form_{{ fieldName }}" avec "01/01/2018"
{% else %}
Etant donné je remplis "{{ entity_class |lower }}_form_{{ fieldName }}" avec "test"
{% endif %}
{% endfor %}
Etant donné je presse "Enregistrer"
Alors le code de status de la réponse devrait être 200
Alors je devrais voir "test"

Scénario: Filtrage
{% for fieldName, data in fields %}
{% if fieldName|slice(0,4) == "date" %}
Etant donné je remplis "filter_value_{{ fieldName }}_main" avec "01/01/2018"
{% else %}
Etant donné je remplis "filter_value_{{ fieldName }}_main" avec "test"
{% endif %}
{% endfor %}
Etant donné je presse "Filtrer"
{% for fieldName, data in fields %}
{% if fieldName|slice(0,4) == "date" %}
Alors le champ "filter_value_{{ fieldName }}_main" devrait contenir "01/01/2018"
{% else %}
Alors le champ "filter_value_{{ fieldName }}_main" devrait contenir "test"
{% endif %}
{% endfor %}
Alors je devrais voir "test"
Alors le code de status de la réponse devrait être 200
