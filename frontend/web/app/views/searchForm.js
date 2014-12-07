define([ "backbone", "jquery","handlebars", "text!tpl/searchForm.html", "views/searchList" ],
    function(Backbone, $, Handlebars, html, SearchList ) {

    return Backbone.View.extend({

        template: '',

        showList: {},

        events: {
            "click .js-search_btn": "search",
            "click .js-search_type": "srType",
            "click .js-search_animal": "srAnimal"
        },


        initialize: function() {

            Handlebars.registerHelper('equal', function(lvalue, rvalue, options) {
                if (arguments.length < 3)
                    throw new Error("Handlebars Helper equal needs 2 parameters");
                if( lvalue!=rvalue ) {
                    return options.inverse(this);
                } else {
                    return options.fn(this);
                }
            });

            this.template = html;
            this.render();
        },

        render: function() {

            var tpl = Handlebars.compile( this.template );
            this.$el.html( tpl( { filter: app.Filter.attributes } ) );

            this.showList = new SearchList( {} );
        },

        search: function() {

            app.Filter.set({
                type: $('input[name=type]').val().split(','),
                animal: $('input[name=animal]').val().split(',')
            });

            this.showList.refresh();
        },

        srType: function( itm ) {
            var res = [];
            $( itm.currentTarget).toggleClass( 'map__radiolineon' );
            $('.map__radiolineon').each( function() {
                res.push( $(this).attr( 'tp' ) );
            });
            $('input[name=type]').val( res );
        },

        srAnimal: function( itm ) {
            var res = [];
            $( itm.currentTarget).toggleClass( 'map__radiobtnon' );
            $('.map__radiobtnon').each( function() {
                res.push( $(this).attr( 'tp' ) );
            });
            $('input[name=animal]').val( res );
        }


    });


});