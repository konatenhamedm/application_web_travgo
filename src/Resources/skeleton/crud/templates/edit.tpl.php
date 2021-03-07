{% block page_content %}
    {% form_theme form 'widget/fields-block.html.twig' %}
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modification <?= $entity_class_name ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i aria-hidden="true" class="ki ki-close"></i>
        </button>
    </div>
    {{ form_start(form, {'attr': {'role':'form', 'class': 'form'}}) }}
    <div class="modal-body">
        {# {{ include('_includes/ajax/response.html.twig') }} #}
        {{ form_widget(form) }}
    </div>
    <div class="modal-footer">
        {# {{ include('_includes/ajax/loader.html.twig') }} #}
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary btn-ajax">Valider</button>
    </div>
    {{ form_end(form) }}
{% endblock %}

