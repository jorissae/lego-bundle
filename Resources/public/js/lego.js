
/*
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

//write by joris
$(function(){
    $("input.datepicker" ).datepicker({'dateFormat':'dd/mm/yy' ,'changeMonth': true,'changeYear': true });

    $("select.select2").select2({'width':'100%'});
    $( document ).ajaxStop(function() {
        $("select.select2").select2({'width':'100%'});
    });

    $("select.select2[multiple]").select2({
        templateResult: function formatState (state) {
            if (!state.id) { return state.text; }
            if(state.selected){
                var $state = $('<span><input type="checkbox" checked/>' + state.text + '</span>');
            }else{
                var $state = $('<span><input type="checkbox"/>' + state.text + '</span>');
            }
            return $state;
        }
    });

    $('body').on('click', '.lego-bulk-button', function (evt) {
        evt.preventDefault();
        var container_checkbox_id = $(this).attr('data-container-id');
        var component_url = $(this).attr('data-component-url');
        var component_id =  $(this).attr('data-component-id');
        var ids = [];
        $('#'+container_checkbox_id+' .lego-bulk-checkbox').each(function (evt) {
            if ($(this).is(':checked')) {
                ids.push(parseInt($(this).val()));
            }
        });
        lego.post($(this).attr('href'), {
            'ids': ids
        }, function (data) {
            if(data.status == 'ok'){
                var msg = data.message;
                lego.post(component_url, {},function(data){
                    $('#'+component_id).html(data.html);
                    lego.success(msg);
                });
            }
        });
    });

    $('body').on('change', '.lego-bulk-combobox', function (evt) {
        evt.preventDefault();
        var container_checkbox_id = $(this).attr('data-container-id');
        var component_url = $(this).attr('data-component-url');
        var component_id =  $(this).attr('data-component-id');
        var ids = [];
        $('#'+container_checkbox_id+' .lego-bulk-checkbox').each(function (evt) {
            if ($(this).is(':checked')) {
                ids.push(parseInt($(this).val()));
            }
        });
        lego.post($(this).attr('data-url'), {
            'ids': ids,
            'value': $(this).val(),
        }, function (data) {
            if(data.status == 'ok'){
                lego.post(component_url, {},function(data){
                    $('#'+component_id).html(data.html);
                });
                lego.success(data.message);
            }
        });
    });

    $('body').on('click','.lego-edit-in-place',function(){
        $(this).hide();
        $('#'+ $(this).attr('data-span-in-id')).show();
        $('#'+ $(this).attr('data-input-id')).focus();
        if($(this).attr('data-callback') && window[$(this).attr('data-callback')]){
            window[$(this).attr('data-callback')]();
        }
        $("select.select2").select2();
    });

    $('body').on('click','.lego-edit-in-place-close',function(){
        var span = $('#' + $(this).attr('data-span-id'));
        var span_in = $('#'+$(this).attr('data-span-in-id'));
        span.show();
        span_in.hide();
    });

    $('body').on('click','.lego-edit-in-place-eraser',function(){
        $('#'+$(this).attr('data-input-id')).val(null);
        $(this).siblings('.lego-edit-in-place-ok').click();
    });

    $('body').on('click','.lego-edit-in-place-bool',function(){
        var elm = $(this);
        var id = elm.attr('data-item-id');
        var fieldName = elm.attr('data-field-name');
        var val = (parseInt(elm.attr('data-value')) > 0)? 0:1;
        var reload = ($(this).attr('data-reload'))? $(this).attr('data-reload'):'value';
        var line = ($(this).attr('data-line'))? $(this).attr('data-line'):null
        var field= ($(this).attr('data-line'))? $(this).attr('data-field'):null;
        $.ajax({
            method: "POST",
            url: $(this).attr('data-target'),
            data: { id: id, fieldName: fieldName,value: val,cls: '',reload: reload },
            dataType: "json",
        }).done(function( retour ) {
            if(retour.code == 'NOK'){
                alert('Une erreur est survenue ('+retour.err+')');
            }else{
                if(reload == 'entity' && line) {
                    $('#' + line).replaceWith(retour.val);
                }else if(reload == 'field' && field){
                    $('#' + field).replaceWith(retour.val);
                }else{
                    if(retour.val == 1 || retour.val == "1" || retour.val == "oui" || retour.val == 'true'){
                        elm.removeClass('fa-square-o');
                        elm.addClass('fa-check-square-o');
                        elm.attr('data-value',1);
                    }else{
                        elm.removeClass('fa-check-square-o');
                        elm.addClass('fa-square-o');
                        elm.attr('data-value',0);
                    }
                }

            }
        }).fail(function( error ){
            elm.html('<i style="color:red" class="fa fa-warning"></i>');
        });
    });

    $('body').on('click','.lego-edit-in-place-ok',function(){
        var elm = $(this);
        elm.html('<i class="fa fa-spinner"></i>');
        var id = $(this).attr('data-item-id');
        var callback = $(this).attr('data-callback');
        var fieldName = $(this).attr('data-field-name');
        var cls = $(this).attr('data-class');
        var reload = ($(this).attr('data-reload'))? $(this).attr('data-reload'):'value';
        var line = ($(this).attr('data-line'))? $(this).attr('data-line'):null;
        var field= ($(this).attr('data-line'))? $(this).attr('data-field'):null;
        var input = $('#'+ $(this).attr('data-input-id'));
        var val = 0;
        if(input.attr('type') == 'checkbox'){
            val = input.is(':checked')
        }else{
            val = input.val();
        }
        var span = $('#'+ $(this).attr('data-span-id'));
        var span_in = $('#'+ $(this).attr('data-span-in-id'));
        $.ajax({
            method: "POST",
            url: $(this).attr('data-target'),
            data: { id: id, fieldName: fieldName,value: val,cls: cls,reload: reload},
            dataType: 'json',
        }).done(function( retour ) {
            if(retour.code == 'NOK'){
                elm.html('<i style="color:red" class="fa fa-check-circle"></i> ('+retour.err+')');
                input.val(retour.val);
            }else{
                if(reload == 'entity' && line) {
                    $('#' + line).replaceWith(retour.val);
                }else if(reload == 'field' && field){
                    $('#' + field).replaceWith(retour.val);
                }else{
                    elm.html('<i style="color:#00a65a;" class="jsa-click fa fa-save"></i>');
                    input.val(retour.val);
                    span_in.hide();
                    if(retour.val) span.html(retour.val); else span.html('<em>&nbsp;</em>');
                    span.show();
                }
            }
            if(callback) window[callback](retour,elm);
        }).fail(function( error ){
            elm.html('<i style="color:red" class="fa fa-check-circle"></i> (Error '+error.status+')');
            lego.error(error.statusText + '<br/>Look Xhr response');
        });
    });

    $('body').on('change','.lego-choice-page',function(evt){
        var elm = $(this);
        var id = elm.attr('data-target');
        var min = parseInt(elm.attr('data-first'));
        var max = parseInt(elm.attr('data-last'));
        if(elm.val() < min) elm.val(min);
        if(elm.val() > max) elm.val(max);
        $('#' + id + ' .lego-choice-page').each(function(evt){
            $(this).val(elm.val());
        })
    });

    $('body').on('change','.lego-choice-entity-per-page',function(evt){
        var elm = $(this);
        var id = elm.attr('data-target');
        var min = 1;
        if(elm.val() < min) elm.val(min);
        $('#' + id + ' .lego-choice-entity-per-page').each(function(evt){
            $(this).val(elm.val());
        })
    });

    $('body').on('keyup','.lego-choice-page',function(evt){
        if(evt.keyCode == 13){
            var id = $(this).attr('data-target');
            $('#' + id + ' .lego-choice-page-action').first().click();
        }
    });

    $('body').on('keyup','.lego-choice-entity-per-page',function(evt){
        if(evt.keyCode == 13){
            var id = $(this).attr('data-target');
            $('#' + id + ' .lego-choice-entity-per-page-action').first().click();
        }
    });

    $('body').on('click','.lego-choice-page-action',function(evt){
        var id = $(this).attr('data-target');
        var elm = $('#' + id + ' .lego-choice-page').first();
        $('#' + id + ' .lego-choice-page-action').addClass('fa-spin');
        var link = '<a data-target="' + elm.attr('data-target') + '" data-callback="jLoadInTarget" href="' + elm.attr('data-url') + '?page=' + elm.val() + '"></a>';
        jsa.ajax($(link));
    });

    $('body').on('click', '.lego-choice-entity-per-page-action', function(evt){
        var id = $(this).attr('data-target');
        var elm = $(this).parent().prev();
        if(elm.is(':visible')){
            $('#' + id + ' .lego-choice-entity-per-page-action').addClass('fa-spin');
            var inputElm = $('#' + id + ' .lego-choice-entity-per-page').first();
            var separator = (inputElm.attr('data-url').indexOf('?') > 0)? "&":"?";
            var link = '<a data-target="' + inputElm.attr('data-target') + '" data-callback="jLoadInTarget" href="' + inputElm.attr('data-url') + separator + 'nbepp=' + inputElm.val() + '"></a>';
            jsa.ajax($(link));
        }else{
            elm.show();
            $(this).css('margin-left','-25px');
        }
    });

    $('body').on('click', '.lego-breaker-collapse', function(evt){
        var elm = $(this);
        elm.removeClass('fa-minus');
        elm.removeClass('fa-plus');
        var breaker = $('#' + $(this).attr('data-breaker'));
        if(breaker.is(':visible')){
            elm.addClass('fa-plus');
            breaker.hide();
        }else{
            elm.addClass('fa-minus');
            breaker.show();
        }
    });

    $('body').on('change', 'input[type=checkbox].cascade', function(evt){
        var self = $(this);
        $('input[type=checkbox].' + self.attr('data-cascade')).each(function(){
            $(this).prop('checked',self.is(':checked'));
        })
    })


});

var lego = {
    post: function(url, params, callback, errorCallback){

        $.ajax({
            type        : 'POST',
            url         : url,
            data        : params,
            dataType    : 'json',
            success     : function(data) {
                callback(data);
            },
            error : function(xhr, ajaxOptions, thrownError){
                if (errorCallback) errorCallback(elm, xhr, ajaxOptions, thrownError);
            }
        });
    },

    filter_set_button: function(id, id_button, val) {

        $(id).val(val);
        $(id_button + ' .fa').hide();
        $(id_button + ' .'+val).show();
        return false;
    },

    success: function(message){
        $('<div>'+message+'</div>').dialog({title:'Success'});
    },

    error: function(message){
        $('<div>'+message+'</div>').dialog({title:'Error'});
    }
};

if(jsa) {
    jsa.evt.legoReloadLine = function (elm, data) {
        $('#' + elm.attr('data-line')).replaceWith(data);
    };

    jsa.evt.legoDeleteLine = function (elm, data) {
        if (data.status == 'ok') {
            $('#' + elm.attr('data-line')).hide();
        } else {
            this.error(data.message);
        }
    };

    jsa.evt.legoPreChoiceBreaker = function (elm, data) {
        console.log('ok');
        var elm = $(elm).parents('.btn-group').first().find('.caret');
        elm.removeClass();
        elm.addClass('fa fa-spinner fa-pulse fa-fw');
    };
}
