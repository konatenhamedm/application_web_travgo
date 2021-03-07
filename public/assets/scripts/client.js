$(document).ready(function() {
    //const $table_contact = $('#table-contact');
    var $addLink = $('.add_contact');
    var $container = $('#contact-list');
    // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
    $addLink.click(function(e) {
        e.stopImmediatePropagation();
        addLine($container);
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
    });
    // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
    var index = $container.find('div.line').length;
    // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un (cas d'une nouvelle annonce par exemple).
    if (index == 0) {
        addLine($container);
    } else {
        if (index > 0) {
            // Pour chaque echantillon déjà existante, on ajoute un lien de suppression
            $container.children('tr.prototype_contact').each(function() {
                const $this = $(this);
                $this.find("select").each(function() {
                    const $this = $(this);
                    $this.select2($this.hasClass('select2_ajax') ? selectedOptions : {});
                });
                if (!$this.hasClass('no-delete')) {
                    addDeleteLink($this);
                }
            });
        }
    }
     // La fonction qui ajoute un formulaire Categorie
    function addLine($container) {
        // Dans le contenu de l'attribut « data-prototype », on remplace :
        // - le texte "__name__label__" qu'il contient par le label du champ
        // - le texte "__name__" qu'il contient par le numéro du champ
        var $prototype = $($("div#list-contact").attr('data-prototype').replace(/__name__label__/g, 'contact ' + (index + 1)).replace(/__name__/g, index));
        // On ajoute au prototype un lien pour pouvoir supprimer la prestation
        addDeleteLink($prototype);
        // On ajoute le prototype modifié à la fin de la balise <div>
        $container.append($prototype);
        $prototype.find("select").each(function() {
            const $this = $(this);
            $this.select2();
        });
        index++;
    }
    // La fonction qui ajoute un lien de suppression d'une prestation
    function addDeleteLink($prototype) {
        // Création du lien
        //$deleteLink = $('<a href="#" class="btn btn-danger btn-sm"><i class="fa fa-remove"></i></a>');
        $deleteLink = $('<a href="#" data-repeater-delete="" class="btn font-weight-bold btn-danger btn-icon"><i class="la la-remove"></i></a>')
        // Ajout du lien
        $prototype.find("div.delete").append($deleteLink);
        // Ajout du listener sur le clic du lien
        $deleteLink.click(function(e) {
            const $parent = $(this).closest('div.line');
            $parent.remove();
            if (index > 0) {
                index -= 1;
            }
            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        });
    }

});