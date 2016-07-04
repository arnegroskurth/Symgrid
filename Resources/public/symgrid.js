
(function($) {

    // Symgrid requires jQuery
    if(!$) {

        window.onload = function() {

            var elements = document.getElementsByClassName('symgrid');

            for(var i = 0; i < elements.length; i++) {
                elements[i].innerHTML = 'Symgrid requires jQuery!';
            }
        };

        return;
    }


    /**
     * Redirects the user to some url.
     *
     * @param url Target url.
     * @param data Serializable data to send as parameters.
     * @param method Http method to use. Defaults to 'GET'.
     * @param target Target frame. Defaults to '_self'.
     */
    var redirect = function(url, data, method, target) {

        data = (typeof data == 'string') ? data : $.param(data);

        var markup = '<form action="'+ (url || '') +'" method="'+ (method || 'get') +'" target="' + (target || '_self') + '">';
        $.each(data.split('&'), function() {

            var pair = this.split('=');
            markup += '<input type="hidden" name="' + decodeURIComponent(pair[0]) + '" value="' + decodeURIComponent(pair[1]) + '" />';
        });
        markup += '</form>';

        $(markup).appendTo('body').submit().remove();
    };


    /**
     * Central class representing a grid.
     *
     * @param container Div element containing the grid.
     * @constructor
     */
    var Grid = function(container) {

        var eventListeners = {
            change: [],
            rowClick: []
        };


        /**
         * Calls all registered eventListeners on a given event.
         *
         * @param event
         */
        var callEventListeners = function(event) {

            $.each(eventListeners[event], function(index, callback) {

                callback();
            });
        };


        /**
         * @returns string Grid title.
         */
        this.getTitle = function() {

            return $(container).attr('data-title');
        };


        /**
         * @returns string Grid identifier.
         */
        this.getIdentifier = function() {

            return $(container).attr('data-identifier');
        };


        /**
         * @returns bool Whether this grid has selectable rows.
         */
        this.isSelectable = function() {

            return $(container).hasClass('selectable');
        };


        /**
         * Registers an event listener on a given event name.
         *
         * @param event Event name to bind callback to.
         * @param fn Callback function.
         * @returns {Grid}
         */
        this.on = function(event, fn) {

            if(eventListeners[event] instanceof Array) {

                eventListeners[event].push(fn);

                this.setupEventHandlers();
            }

            return this;
        };


        /**
         * Registers a callback function that gets called when the user clicks on a data row.
         *
         * @param fn Function to call on click on row.
         * @returns {Grid}
         */
        this.onRowClick = function(fn) {

            return this.on('rowClick', fn);
        };


        /**
         * Registers a callback function that gets called when the grid is changed e.g. after a filter has been changed.
         *
         * @param fn Function to call when grid is changed.
         * @returns {Grid}
         */
        this.onChange = function(fn) {

            return this.on('change', fn);
        };


        /**
         * Sets up all event handlers for this grid.
         * This is triggered after the grid (or parts of it) are refreshed.
         *
         * @returns {Grid}
         */
        this.setupEventHandlers = function() {

            // prevent default form submission
            $(container).filter('.liveupdateable').find('form').off().submit(function(e) {

                e.preventDefault();
            });


            // add class to row when selected for group action
            $(container).find('.row-select input').off().click(function(e) {

                $(this).closest('tr.data-row').toggleClass('selected');

                // un-check all-rows-selectors
                if(!this.checked) {
                    $(this).closest('table').find('.row-select-all input').prop('checked', false);
                }

                e.stopPropagation();
            });
            $(container).find('.row-select').off().click(function(e) {

                $(this).find('input').click();

                e.stopPropagation();
            });

            // select all rows
            $(container).find('.row-select-all input').off().click(function(e) {

                var table = $(this).closest('table');
                var dataRows = table.find('tr.data-row');

                table.find('.row-select input').prop('checked', this.checked);

                if(this.checked) {
                    dataRows.addClass('selected');
                }
                else {
                    dataRows.removeClass('selected');
                }

                e.stopPropagation();
            });


            // click somewhere on row
            if(this.isSelectable() || eventListeners.rowClick.length > 0) {

                // prevent click-event propagation for clickables in grid content
                var tds = $(container).find('tbody td:not(.row-select)');
                tds.find('a').off().click(function(e) { e.stopPropagation(); });
                tds.find('input').off().click(function(e) { e.stopPropagation(); });
                tds.find('button').off().click(function(e) { e.stopPropagation(); });

                // setup custom callback
                if(eventListeners.rowClick.length) {

                    $.each(eventListeners.rowClick, function(index, callback) {

                        $(container).find('tbody tr').click(callback);
                    });
                }

                // setup row selection callback
                else {

                    $(container).find('tbody tr').click(function(e) {

                        $(this).find('.row-select input').click();

                        e.stopPropagation();
                    });
                }
            }


            // filter update on liveupdateable grids
            $(container).filter('.liveupdateable').find('.filter').off().change(function() {

                $(container).find('select[name=_groupAction]').val('');

                $(this).Symgrid().apply();
            });

            // explicit submission button
            $(container).find('input[type=submit]').off().click(function() {

                $(this).Symgrid().apply();
            });


            // export
            $(container).find('input[name=_export]').off().click(function() {

                redirect(null, $(container).find('form').serialize() + '&_export=' + $(this).val() + '&' + window.location.search.slice(1));
            });


            // sorting
            $(container).filter('.sortable').find('thead .sort').off().click(function(e) {
                
                var columnHead = $(this).closest('th');
                var direction = 'desc';

                if(columnHead.hasClass('order-desc')) {
                    direction = 'asc';
                }

                $(container).find('select[name=_groupAction]').val('');
                $(this).Symgrid().orderBy(columnHead.attr('data-column'), direction);

                e.preventDefault();
            });


            // paging
            $(container).find('select[name=_page]').off().change(function() {

                $(this).Symgrid().apply('tbody');
            });


            // grid reset
            $(container).find('input[type=reset]').off().click(function(e) {

                e.preventDefault();

                $(container).find('input[name=_orderPath]').val('');
                $(container).find('input[name=_orderDirection]').val('');
                $(container).find('.filters input, .filters select').val('');
                $(container).find('.row-select input').attr('checked', false);
                $(container).find('select[name=_groupAction]').val('');
                $(container).find('select[name=_page]').val('1');

                $(this).Symgrid().apply('table');
            });


            return this;
        };


        /**
         * Revalidates the grid according to set filters.
         *
         * @returns {Grid}
         */
        this.apply = function(parts) {

            // handle invoked group action
            if($(container).find('select[name=_groupAction]').val()) {

                var groupActionOption = $(container).find('select[name=_groupAction] option:selected');
                var message = groupActionOption.attr('data-message');
                var parameterName = groupActionOption.attr('data-parameter-name');

                var recordsIds = $(container).find('.row-select input:checked').map(function() {
                    return $(this).val();
                }).get();

                if(recordsIds.length) {

                    if(!message || confirm(message)) {

                        var data = {};
                        data[parameterName] = recordsIds;

                        redirect(groupActionOption.attr('data-target-url'), data, groupActionOption.attr('data-method'), groupActionOption.attr('data-target'));
                    }
                }

                return this;
            }

            // handle changed grid state
            $(container).addClass('loading');
            $.ajax({

                data: $(container).find('form').serialize() + '&_parts=' + (parts || 'tbody,tfoot'),

                error: function () {

                    $(container).addClass('with-error');
                },

                success: function(data) {

                    // replace entire grid
                    if(data.table) {

                        $(container).find('table').replaceWith(data.table);
                    }

                    // replace grid content
                    if(data.tbody) {

                        $(container).find('tbody').replaceWith(data.tbody);
                    }

                    // replace grid footer
                    if(data.tfoot) {

                        $(container).find('tfoot').replaceWith(data.tfoot);
                    }

                    $(container).removeClass('with-error');
                    $(container).Symgrid().setupEventHandlers();

                    callEventListeners('change');
                },

                complete: function() {

                    $(container).find('.count .displayed').html($(container).find('table tbody tr').length);
                    $(container).removeClass('loading');
                }
            });

            return this;
        };


        /**
         *
         *
         * @param columnIdentifier
         * @param direction Either "asc" or "desc".
         * @returns {Grid}
         */
        this.orderBy = function(columnIdentifier, direction) {

            $(container).find('th').removeClass('order order-asc order-desc');

            var columnHead = $(container).find('th[data-column=' + columnIdentifier + ']');
            columnHead.addClass('order order-' + direction);

            $(container).find('input[name=_orderPath]').val(columnHead.attr('data-path'));
            $(container).find('input[name=_orderDirection]').val(direction);

            this.apply('tbody');

            return this;
        };
    };


    /**
     * Manages instances of {Grid} and makes them accessible globally.
     */
    window.Symgrid = {

        grids: [],

        getGrid: function(e) {

            if(!(e instanceof jQuery)) e = $(e);

            if(!e.hasClass('symgrid')) e = e.closest('.symgrid');

            if(e.hasClass('symgrid')) {

                var identifier = e.attr('data-identifier');

                if(typeof this.grids[identifier] == 'undefined') {

                    this.grids[identifier] = new Grid(e);
                }

                return this.grids[identifier];
            }

            return null;
        }
    };


    /**
     * jQuery plugin allows to retrieve {Grid} object from jQuery object representing the grid container or some child of it.
     *
     * @returns {Grid}
     */
    $.fn.Symgrid = function() {

        return window.Symgrid.getGrid(this);
    };


    $(document).ready(function() {

        // trigger initial loading
        $('.symgrid').each(function() {

            $(this).Symgrid().apply();
        });
    });

})(window.jQuery);


