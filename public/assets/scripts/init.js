function init_select2(selector = null, placeholder = null, dropDownParent = 'body', initials = []) 
{
    if (selector) {
        if (selector == 'select') {
            $selector = $('select').filter(function () {
                return !this.closest('.grid-wrapper');
            });
        } else {
            $selector = $(selector);
        }
    } else {
        $selector = $('.select2_single, .select2_multiple, .select2-multiple, .select2-single, .select2');
    }

    $selector.each(function () {
        let $this = $(this);
        const placeholder = $this.attr('placeholder') || $this.data('select2-placeholder');
        const multiple = $this.prop('multiple');
        const default_placeholder = multiple ? 'Selectionner au moins un élément de la liste': 'Sélectionner un élément de la liste';
        let $item = $this.select2({
            //placeholder: placeholder || 
            //tags: tag,
            //multiple: multiple,
            tokenSeparators: [","],
            data: initials,
            dropdownParent: $(dropDownParent || 'body'),
        
            //dropDownParent: dropDownParent,

            escapeMarkup: function(markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 0,
            /*templateResult: format_data, // omitted for brevity, see the source of this page
            templateSelection: format_data_selection, // omitted for brevity, see the source of this page*/
            noResults: function() {
                return 'Aucun résultat';
            },
            searching: function() {
                return 'Recherche…';
            }
    });

    if (Array.isArray(initials) && initials.length) {
        let ids = [];
        initials.forEach((initial) => {
            ids.push(initial.id);
        });

        $item.val(ids).trigger('change');
    }
    });

}

function init_date_picker(selector = null, drops = 'down', cb = null, minYear = null, maxYear = null, autoUpdateInput=true, minDate = null, maxDate = null) {
            let format = 'DD/MM/YYYY';
            let timepicker = false;

            if (selector == '.datetimepicker') {
                format += ' HH:mm';
                timepicker = true;
            }

            /*if (selector == 'daterangetimepicker') {
                format = 'HH:mm:mm';
                timepicker = true;
            }*/

            var minDate = null;
            var maxDate = null;
            var $selector = $(selector ? selector : '.datepicker');

            console.log($selector);

            if ($selector.hasClass('datetimepicker') && !timepicker) {
                format += ' HH:mm';
                timepicker = true;
            }

            if (!maxYear && !minYear) {
                var d = new Date(); 

                minYear = d.getFullYear() - 1
                maxYear = d.getFullYear()
            }

            if (minYear && !minDate) {
                minDate = '01/01/'+minYear;
            }

            if (maxYear && !maxDate) {
                maxDate = '31/12/'+maxYear;
            }

           
            let cbs = [];
           
            $selector.each(function (index, current) {
                var $this = $(this);
                
                if (!autoUpdateInput && !cb) {
                    cb = (start, e) => {
                        $this.val(start.format(format));
                    };
                }

                $this.daterangepicker({
                    singleDatePicker: true,
                    autoUpdateInput: autoUpdateInput,
                    showDropdowns: true,
                    timePicker24Hour: timepicker,
                    timePicker: timepicker,
                    maxYear: +maxYear,
                    minYear: +minYear,
                    minDate: minDate,
                    maxDate: maxDate,
                    drops: drops,
                    locale: {
                        format: format,
                        firstDay: 1,
                        "daysOfWeek": 
                            ["Di","Lu","Ma","Me","Je","Ve","Sa"],
                        "monthNames": 
                            [
                                "Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin",
                                "Juillet", "Aôut", "Septembre", "Octobre", "Novembre", "Decembre"
                            ],
                    }
                }, cb);
            });
        }

$(function() {
    $(document).ready(function(){
        init_select2('select')
        init_date_picker()
    })
})