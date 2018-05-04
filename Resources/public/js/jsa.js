$(function(){

    $('a[data-confirm]').on('click', function () {
        return confirm($(this).attr('data-confirm'));
    });

    $('body').on('click','.jsa-shower',function(e){
        e.preventDefault();
        $('.jsa-showable').hide();
        $('#'+$(this).attr('data-show')).show();
    });


    $('body').on('click','.jsa-send-form-ajax',function(e){
        e.preventDefault();
        jsa.ajaxForm($('#'+$(this).attr('data-form')),window[$(this).attr('data-callback')],$(this).attr('data-type'),$(this),$(this).attr('data-action'));
    });

    $('body').on('change','select.jsa-ajax',function(e){
        e.preventDefault();
        jsa.ajax($(this));
    });

    $('body').on('click','a.jsa-ajax, span.jsa-ajax, img.jsa-ajax, div.jsa-ajax, button.jsa-ajax',function(e){
        e.preventDefault();
        var elm = $(this);
        if(elm.attr('data-confirmation')){
            bootbox.confirm(elm.attr('data-confirmation'),function(result){if(result) jsa.ajax(elm);});
        }else{
            jsa.ajax(elm);
        }
    });

    $('body').on('click','.jsa-submit',function(e) {
        e.preventDefault();
        var form = $('#'+$(this).attr('data-form'));
        form.submit();
    });

    $('body').on('click','.jsa-scroll',function(e) {
        $('html,body').animate({scrollTop: $($(this).attr('href')).offset().top}, 'slow');
    });

    $('body').on('click', '.jsa-open-popup',function(e){
        jsa.popup({url : $(this).attr('data-url'),width: $(this).attr('data-width'),'title': $(this).attr('data-title')});
    });

    $('body').on('click','.jsa-open-dialog-form',function(e){
        var elm = $(this);
        var callback = (window[elm.attr('data-callback')])? window[elm.attr('data-callback')]:jsa_evt[elm.attr('data-callback')];
        var type = (elm.attr('data-type'))? elm.attr('data-type'):'json';
        var action = (elm.attr('data-action'))? elm.attr('data-action'):null;
        action = (elm.attr('href'))? elm.attr('href'):action;
        var dialog = $('#'+elm.attr('data-target')).dialog({
            title: ($(this).attr('data-title'))? $(this).attr('data-title'):'Formulaire',
            autoOpen: true,
            height: (elm.attr('data-height'))? elm.attr('data-height'):200,
            width: (elm.attr('data-width'))? elm.attr('data-width'):350,
            modal: true,
            buttons: {
                Ok: function(){
                    if(callback){
                        jsa.ajaxForm(dialog.find("form"), callback, type, elm, action);
                        dialog.dialog( "close" );
                    }else{
                        dialog.find("form").submit();
                    }
                },
                Cancel: function() {
                    dialog.dialog( "close" );
                }
            }
        });
        dialog.find("form").attr('action',$(this).attr('href'));
        e.preventDefault();
    });

    $('body').on('click','.jsa-form-img',function(e){
        var elm = $(this);
        var group = $(elm.attr('data-group'));
        var target = $('#'+elm.attr('data-target'));
        group.css('border', '0px solid black');
        elm.css('border', '2px solid black');
        target.val(elm.attr('data-value'));
    });

    $('body').on('click','.jsa-popup',function(e){
        var elm = $(this);
        var target = elm.attr('data-popup-id');
        if(target){
            $('<div>'+ $('#'+target).html()+'</div>').dialog({title: elm.attr('title'),'width':550});
        }else {
            $.ajax({
                url: elm.attr('data-url'),
                success: function (data) {
                    $('<div>' + data.html + '</div>').dialog({title: data.title, 'width': (data.width)? data.width:'auto'});
                }
            });
        }
    });

    $( ".jsa-widget-container" ).sortable({
        items: "div.jsa-widget",
        //placeholder: "jsa-widget-holder",
        tolerance: 'pointer',
        //revert: 'invalid',
        forceHelperSize: false,
        update: function(evt,ui){
            jsa.save_widget($(this));
        }
    });

    $('body').on('click','.jsa-add-widget',function(evt){
        var widget_id = $(this).attr('data-widget');
        var container = $(this);
        $.ajax({
            type        : 'post',
            url         : $(this).attr('data-url'),
            dataType    : 'html',
            success     : function(data) {
                $("#jsa-widget-in-list-" + widget_id).hide('slide');
                $( ".jsa-widget-container").append($(data));
                jsa.save_widget();
            }
        });
    });

    $('body').on('click','.jsa-remove-widget',function(evt){
        var widget_id = $(this).attr('data-widget-id');
        $("#jsa-widget-" + widget_id).remove();
        jsa.save_widget();
    });
    jsa.init_tabs();



});

var jsa = {

    load_jsa_ajax_content: function(){
        $('.jsa-ajax-content').each(function(){
            var elm = $(this);
            $.ajax({
                url         : elm.attr('data-url'),
                dataType    : 'html',
                success     : function(data) {
                    elm.replaceWith(data);
                }
            });
        });
    },
    save_widget: function(container){
        var order = []
        $( ".jsa-widget-container").find('div.jsa-widget').each(function(elm){
            order.push($(this).attr('data-widget-id'));
        });
        console.log(order);
        console.log(container.attr('data-widget-order-save'));
        console.log('ok');
        $.ajax({type: 'post', url: container.attr('data-widget-order-save'), data: {order:order}});
    },

    update_elm: function (elm, data){
        if(data.attrs){
            for(var attr in data.attrs){
                elm.attr(attr,data.attrs[attr]);
            }
        }
        if(data.html){
            elm.html(data.html);
        }
        if(data.val){
            elm.val(data.val);
        }
    },

    dialog: function(title,msg){
        $('<div>'+msg+'</div>').dialog({title:title});
    },

    popup: function(options){
        var self = this;
        if(!options.width) options.width = 'auto';
        if(options.url){
            $.ajax({
                url         : options.url,
                dataType    : 'html',
                success     : function(data) {
                    $('<div class="jsa-popup-elm">'+data+'</div>').dialog(options);
                }
            });
        }else if(options.id){
            $('#' + options.id).dialog(options);
        }
    },

    init_tabs: function(){
        $( ".jsa-tabs, .jsa-tabs-v" ).tabs({
            beforeLoad: function( event, ui ) {
                ui.jqXHR.fail(function() {
                    ui.panel.html( "une erreur est survenue" );
                });
            }
        });

        $( ".jsa-tabs-v" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( ".jsa-tabs-v li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
    },

    ajaxForm: function( $form, callback, type , elm, action){
        var type = (type)? type:'json';
        var formdata = (window.FormData) ? new FormData($form[0]) : null;
        var preCallback = (elm.attr('data-pre-callback'))? window[elm.attr('data-pre-callback')]:null;
        if(preCallback){
            preCallback(elm);
        }
        var data = (formdata !== null) ? formdata : $form.serialize();
        var action = (action)? action:$form.attr( 'action' );
        $.ajax({
            type        : $form.attr( 'method' ),
            url         : action,
            contentType : false, // obligatoire pour de l'upload
            processData : false, // obligatoire pour de l'upload
            data        : data,
            dataType    : type,
            success     : function(data) {
                callback( elm, data );
            }
        });
    },

    ajax: function(elm){
        var type = (elm.attr('data-type'))? elm.attr('data-type'):'json';
        var callback = (window[elm.attr('data-callback')])? window[elm.attr('data-callback')]:jsa.evt[elm.attr('data-callback')];
        var preCallback = ( window[elm.attr('data-pre-callback')])? window[elm.attr('data-pre-callback')]:jsa.evt[elm.attr('data-pre-callback')];
        var errCallback = (elm.attr('data-err-callback'))? window[elm.attr('data-err-callback')]:null;
        if(preCallback){
            preCallback(elm);
        }
        var url = (elm.attr('data-url'))? elm.attr('data-url'):elm.attr('href');
        var method = (elm.attr('data-method'))? elm.attr('data-method'):'post';
        var values = {};
        if(elm.attr('data-group')) {
            $(elm.attr('data-group')).each(function () {
                if ($(this).attr('type') == 'radio') {
                    if ($(this).is(':checked')) values[$(this).attr('name')] = $(this).val();
                } else if ($(this).attr('type') == 'checkbox') {
                    if ($(this).is(':checked')) {
                        if (!values[$(this).attr('name')]) values[$(this).attr('name')] = [];
                        values[$(this).attr('name')].push($(this).val());
                    }
                } else {
                    values[$(this).attr('name')] = $(this).val();
                }
            });
        }
        var name = (elm.attr('data-name'))? elm.attr('data-name'):elm.attr('name');
        var donnes = (elm.attr('data-donnes'))? JSON.parse(elm.attr('data-donnes').replace(/\'/gi,'"')):[];
        var data = {'data':donnes,'value':elm.val(),'values':values,'name':name};
        if(elm.attr('data-no')) data = null;
        $.ajax({
            type        : method,
            url         : url,
            data        : data,
            dataType    : type,
            success     : function(data) {
                if (callback) callback(elm, data);
            },
            error : function(xhr, ajaxOptions, thrownError){
                if (errCallback) errCallback(elm, xhr, ajaxOptions, thrownError);
            }
        });
    },


    'evt': {
        jDelete: function(elm,data){
            if(data.status == 'ok'){
                jsa.update_elm(elm,data);
                $(data.target).slideUp("slow");
            } else {
                jsa.dialog('Erreur',data.error);
            }
        },
        jRedirect: function(elm,data){
            jsa.update_elm(elm,data);
            window.location = elm.attr('data-redirect');
        },
        jRefresh: function(elm, data){
            if(data.status == 'ok'){
                if(data.refresh) {
                    for (var k in data.refresh) {
                        if(typeof data.refresh[k] == 'object'){
                            for (var attr in data.refresh[k]){
                                $(k).attr(attr,data.refresh[k][attr]);
                            }
                        }else{
                            $(k).html(data.refresh[k]);
                        }
                    }
                }
            } else {
                jsa.dialog('Erreur',data.error);
            }
        },
        jHide: function(elm,data){
            if(data.status == 'ok'){
                jsa.update_elm(elm,data);
                $(data.target).slideUp("slow");
            } else {
                jsa.dialog('Erreur',data.error);
            }
        },
        jBox: function(elm,data){
            if(data.status == 'ok'){
                jsa.update_elm(elm,data);
                bootbox.alert({title: data.title, message: data.message});
            }else{
                jsa.dialog('Erreur',data.error);
            }
        },
        jLoadInTarget: function(elm, data){
            $('#'+elm.attr('data-target')).html(data.html);
        },
        jPreLoadInSelf: function(elm){
            elm.html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
        }
    }
}
