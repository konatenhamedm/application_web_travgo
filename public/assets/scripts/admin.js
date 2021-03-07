// Charger le contenu de d'un nav-bar à partir de l'url contenue dans celle-ci
function load_tab_content(url, hash, hash_key, url_key) {
    if (hash_key && url_key) {
        localStorage.setItem(hash_key, hash);
        localStorage.setItem(url_key, url);
    }
    $.ajax({
        url: url,
        cache: false,
        beforeSend: function() {
            //console.log(hash);
            $(`#${hash}`).html('<p class="text-center">Chargement des données</p>');
        },
        success: function(content) {
            $(`#${hash}`).empty().html(content);
        },
        error: function(jqXhr, textStatus, errorThrown) {
            let html;
            if (jqXhr.status != 404) {
                html = '<p class="text-center">Erreur interne du serveur</p>';
            } else {
                html = '<p class="text-center">URL introuvable</p>';
            }
            $(`#${hash}`).empty().html(html);
        }
    });
}

function reload_page(url, index = 0, persist_flash = false) {
    $('#page-loader').removeClass('display-none');
    $('.page-content-inner').load(`${url} #page-content-wrapper`, () => {
        $('#page-loader').addClass('display-none');
        //$('.alert-flash').addClass('hide');
        //alert($('.nav-tabs-line').length)
        if ($('.nav-tabs-line').length) {
            const id = $('.nav-tabs-line').attr('id');
            //alert($('.nav-tabs-line').attr('id'))
            const storage_key = `${id}_current_index`.replace('-', '_');
            //alert(storage_key)
            const current_index = localStorage.getItem(storage_key) || index;
            //alert(current_index)
            $(`#${id} li:eq(${current_index}) a`).tab('show');
        }
        $('.alert-flash').each(function() {
            const $this = $(this);
            if (!$this.hasClass('alert-success')) {
                $this.hide();
            } else {
                if (persist_flash) {
                    $this.removeClass('hide');
                } else {
                    $this.slideUp(5000);
                }
            }
        });
        if (localStorage.getItem('reopen_on_page_load')) {
            const [elt, index] = localStorage.getItem('reopen_on_page_load').split('|');
            //alert('elt')
            if ($(elt).length) {
                const $target = $(elt + ' li:eq(' + index + ') a').tab('show');
                const [, hash] = $target.get(0).href.split('#');
                load_tab_content($target.data('href'), hash);
            }
        }
        //$('body').scrollTop($('.alert-flash').position().top);
    });
}

$(function() {

    let modals = new Set();

    $('body').on('hidden.bs.modal', '.modal', function() {
        $(this).removeData('bs.modal');
    });


    // Traitement au moment de la validation, au cliqque du bouton valider
    $(document).on('click', '.btn-ajax', function(e) {
        //Formaulaires AJAX
        e.preventDefault();
        const $this = $(this);
        const $form = $this.closest('form');
        const form_id = $form.attr('id');
        //const $loader = $form.find('.loader');
        const $modal = $this.closest('.modal');
        const $nav = $this.data('nav');

        $form.ajaxSubmit({
            cache: false,
            beforeSend: () => {
                //$loader.removeClass('hide');
                $this.addClass('spinner spinner-white spinner-right')
            },
            complete: () => {
                //$loader.addClass('hide');
                $this.removeClass('spinner spinner-white spinner-right')
            },
            success: (data, status, $xhr, $form) => {
                const close_html = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                const message    = data.message;
                const redirect   = data.redirect;
                const actions    = data.actions;
                console.log(data)
                if (data.statut) {

                    $('.ajax-success', $form).removeClass('hide').html(close_html + message);
                    $('.ajax-error', $form).addClass('hide');
                    if (!redirect || (redirect.indexOf('#') === -1)) {
                        if (redirect && $modal.length && data.modal !== false) {
                            $modal.modal('hide');
                        }
                        if (redirect) {
                            //alert( redirect )
                            reload_page(redirect, 0, data.persistFlash)
                        }
                        if (actions) {
                            switch (actions['action']) {
                                case 'switch_tab':
                                    $(`${actions.target} li:eq(${actions.index}) a`).tab('show');
                                    $('.pointer').remove();
                                break;
                            }
                        }
                    } else {
                        //alert('redirect')
                        const [url, modal_id] = redirect.split('#');
                        let opened_modals = [];
                        modals_array = Array.from(modals);
                        let prev_index = 0;
                        modals_array.forEach((val, index) => {
                            if (val.id == modal_id) {
                                prev_index -= 1;
                            }
                        });
                        $('#' + modal_id).modal('hide');
                        $('#' + modals_array[0]).addClass('reload-page');
                        const $current_modal = $('#' + modals_array[prev_index >= 0 ? prev_index : 0]);
                        const grid_hash = localStorage.getItem('__grid_hash');
                        const $grid_wrapper = $('#grid-wrapper-' + grid_hash, $current_modal);
                        if ($grid_wrapper.length) {
                            const $grid_loader = $grid_wrapper.find('.grid-overlay');
                            $grid_loader.removeClass('display-none');
                            $grid_wrapper.load($('#grid_' + grid_hash).attr('action') + ' #grid-table-' + grid_hash, function() {
                                $grid_loader.addClass('display-none');
                            });
                        } else {
                            
                        }
                    }
                    //$modal.scrollTop($('.ajax-success', $form).position().top);
                    //alert("success")
                } else {
                    let tpl = '';
                    if (Array.isArray(message)) {
                        for (let _message of message) {
                            tpl += `<p class="small">${_message}</p>`;
                        }
                    } else {
                        tpl = message;
                    }
                    $('.ajax-error', $form).removeClass('hide').html(close_html + tpl);
                    $('.ajax-success', $form).addClass('hide');
                    $modal.scrollTop($('.ajax-error', $form).position().top);
                }
            },
            error: (data) => {
                $('.ajax-error', $form).removeClass('hide').html('Erreur interne du serveur');
                $('.ajax-success', $form).addClass('hide');
                $modal.scrollTop($('.ajax-error').position().top);
            }
        });
    }).on('click', '.button-ajax', function(e){
        e.preventDefault();
        const $this = $(this);
        const $form = $this.closest('form');
        const form_id = $form.attr('id');
        //alert('button-ajax')
        $form.ajaxSubmit({
            cache: false,
            beforeSend: () => {
                $this.addClass('spinner spinner-white spinner-right')
            },
            complete: () => {
                $this.removeClass('spinner spinner-white spinner-right')
            },
            success: (data, status, $xhr, $form) => {
                const close_html = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                const message    = data.message;
                const redirect   = data.redirect;
                const actions    = data.actions;
                const entity     = data.entity;
                if (data.statut) {
                    console.log(data.entity)
                    table.row.add( [
                        data.entity['code'],
                        data.entity['libelle']
                    ] ).draw( false );
                    /*$('body').notify({
                        message: message,
                        type: 'success'
                    });*/
                    //$('body').toast(data.message)
                    $form[0].reset()                
                }
            },
            error: (data) => {
                /*$('body').notify({
                    message: data.message,
                    type: 'danger'
                }); */
                //$modal.scrollTop($('.ajax-error').position().top);
            }
        });
    }).on('click', '.prevent-default', function(e) {
        e.preventDefault();
    }).on('click', '.link-param', function(e) {

    }).on('click', '#sticky-submit', function(e){
        console.log('sticky')
        parent  = $(this).closest('div.card')
        form    = parent.find('form:eq(0)')
        form.submit()
    });

/*************************************************MODALES*******************************************************/
    // vider le contenu de la modale à sa fermeture
    $('.modal').on('hide.bs.modal', function(e) {
        const $this = $(this);

        const $target = $(e.target);
        if (!$this.closest('.note-editor').length) {
            modals.delete($this.attr('id'));

            const default_template = `
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="spinner spinner-track spinner-primary mr-15">&nbsp;&nbsp;Chargement des données...</div>
                        <span></span>
                    </div>
                `;

            $this.find('.modal-content').html('').append(default_template);
            if ($this.hasClass('reload-page')) {
                $this.removeClass('reload-page');
                reload_page(current_url);
                $('.alert-flash').remove();
            }
        }
        const current_hash = document.location.hash;
        if (current_hash.indexOf('#modal-ref') === 0) {
            document.location.hash = '';
        }
    });


    // Appel chargement du contenu pendant l'ouverture du modal
	$('.modal').on('show.bs.modal', function(e) {
        const $target = $(e.relatedTarget);
        const $this = $(this);
        const options = $this.data('options');

        if ($target.attr('href') && $target.attr('href')[0] != '#') {
            $this.find('.modal-content').load($target.attr('href'));
        }
    });
  /**********************************************FIN MODALES**********************************************/


	// Vider le contenu du modal à sa fermeture
/*    $('.modal').on('hide.bs.modal', function(e) {
        const $this = $(this);

        const $target = $(e.target);
        if (!$this.closest('.note-editor').length) {
            modals.delete($this.attr('id'));

            const default_template = `
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${loader_path}" alt="" class="loading">
                        <span> &nbsp;&nbsp;Chargement des données... </span>
                    </div>
                `;

            $this.find('.modal-content').html('').append(default_template);
            if ($this.hasClass('reload-page')) {
                $this.removeClass('reload-page');
                reload_page(current_url);
                $('.alert-flash').remove();
            }
        }
        const current_hash = document.location.hash;
        if (current_hash.indexOf('#modal-ref') === 0) {
            document.location.hash = '';
        }
    });
*/})