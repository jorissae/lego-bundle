
//write by joris
$(function(){
    //Commenté car ce listener est déjà en place sur le bundle Avanzu ce qui
    //Créait des double demande de confirmation sur les liens en <a></a>
    /*$('a[data-confirm]').on('click', function () {
        return confirm($(this).attr('data-confirm'));
    });*/

    $('body').on('click','.lle-shower',function(e){
      e.preventDefault();
      $('.lle-showable').hide();
      $('#'+$(this).attr('data-show')).show();
    });


    $("input.datepicker" ).datepicker({'dateFormat':'dd/mm/yy' ,'changeMonth': true,'changeYear': true });

    $("select.select2").select2({'width':'100%'});

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


    $('body').on('click','.lle-send-form-ajax',function(e){
      e.preventDefault();
      lle.ajaxForm($('#'+$(this).attr('data-form')),window[$(this).attr('data-callback')],$(this).attr('data-type'),null,$(this).attr('data-action'));
    });

    $('body').on('change','select.lle-ajax',function(e){
      e.preventDefault();
      lle.ajax($(this));
    });

    $('body').on('click','a.lle-ajax, span.lle-ajax, img.lle-ajax, div.lle-ajax',function(e){
      e.preventDefault();
      lle.ajax($(this));
    });

    $('body').on('click','.lle-submit',function(e) {
      e.preventDefault();
      var form = $('#'+$(this).attr('data-form'));
      form.submit();
    });

    $('body').on('click','.lle-scroll',function(e) {
      $('html,body').animate({scrollTop: $($(this).attr('href')).offset().top}, 'slow');
    });

    $('body').on('keydown', '.edit-in-place-input', function(e) {
        if(e.keyCode == 13) {
            $(this).siblings('.edit-in-place-ok').click();
        }
    });

    $('body').on('click','.edit-in-place',function(){
        $(this).hide();
        $('#span-in-'+ $(this).attr('data-column-name') + '-'+ $(this).attr('data-item-id')).show();
        $('#input-'+ $(this).attr('data-column-name') + '-'+ $(this).attr('data-item-id')).focus();

        $("select.select2").select2();
    });
    $('body').on('click','.edit-in-place-close',function(){
        var columnName = $(this).attr('data-column-name');
        var id = $(this).attr('data-item-id');
        var span = $('#span-'+ columnName + '-' + id);
        var span_in = $('#span-in-'+ columnName + '-' + id);
        span.show();
        span_in.hide();
    });
    $('body').on('click','.edit-in-place-eraser',function(){
        $('#input-'+ $(this).attr('data-column-name') + '-'+ $(this).attr('data-item-id')).val(null);
        $(this).siblings('.edit-in-place-ok').click();
    });    

    $('body').on('click','.edit-in-place-bool',function(){
        var elm = $(this);
        var id = elm.attr('data-item-id');
        var columnName = elm.attr('data-column-name');
        var val = (parseInt(elm.attr('data-value')) > 0)? 0:1;
        var reload = ($(this).attr('data-reload'))? $(this).attr('data-reload'):'td';
        var line = ($(this).attr('data-line'))? $(this).attr('data-line'):null;
        $.ajax({
          method: "POST",
          url: $(this).attr('data-target'),
          data: { id: id, columnName: columnName,value: val,cls: '',reload: reload },
          dataType: "json",
        }).done(function( retour ) {
            if(retour.code == 'NOK'){
                alert('Une erreur est survenue ('+retour.err+')');
            }else{
              if(reload == 'tr' && line){
                $('#'+line).replaceWith(retour.val);
                $("select.select2").select2();
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

    $('body').on('click','.edit-in-place-ok',function(){
        var elm = $(this);
        elm.html('<i class="fa fa-spinner"></i>');
        var id = $(this).attr('data-item-id');
        var callback = $(this).attr('data-callback');
        var columnName = $(this).attr('data-column-name');
        var cls = $(this).attr('data-class');
        var reload = ($(this).attr('data-reload'))? $(this).attr('data-reload'):'td';
        var line = ($(this).attr('data-line'))? $(this).attr('data-line'):null;
        var input = $('#input-'+ columnName + '-' + id);
        var val = 0;
        if(input.attr('type') == 'checkbox'){
            val = input.is(':checked')
        }else{
            val = input.val();
        }
        var span = $('#span-'+ columnName + '-' + id);
        var span_in = $('#span-in-'+ columnName + '-' + id);
        $.ajax({
          method: "POST",
          url: $(this).attr('data-target'),
          data: { id: id, columnName: columnName,value: val,cls: cls,reload: reload },
          dataType: 'json',
        }).done(function( retour ) {
            if(retour.code == 'NOK'){
                elm.html('<i style="color:red" class="fa fa-check-circle"></i> ('+retour.err+')');
                input.val(retour.val);
            }else{
                if(reload == 'tr' && line){
                  $('#'+line).replaceWith(retour.val);
                  $("select.select2").select2();
                }else{
                  elm.html('<i class="fa fa-check-circle"></i>');
                  input.val(retour.val);
                  span_in.hide();
                  if(retour.val) span.html(retour.val); else span.html('<em>&nbsp;</em>');
                  span.show();
                }
            }
            if(callback) window[callback](retour,elm);
        }).fail(function( error ){
            elm.html('<i style="color:red" class="fa fa-warning"></i>');
        });
    });

    $('body').on('click','.open-dialog-form',function(e){
        var elm = $(this);
        var callback = elm.attr('data-callback');
        var type = (elm.attr('data-type'))? elm.attr('data-type'):'json';
        var dialog = $(elm.attr('data-target')).dialog({
          title: ($(this).attr('data-title'))? $(this).attr('data-title'):'Formulaire',
          autoOpen: true,
          height: (elm.attr('data-height'))? elm.attr('data-height'):200,
          width: (elm.attr('data-width'))? elm.attr('data-width'):350,
          modal: true,
          buttons: {
            Ok: function(){
                if(callback){
                  lle.ajaxForm(dialog.find("form"),window[callback],type,elm);
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


    $('body').on('click','.lle-popup',function(e){
        var elm = $(this);
        var target = elm.attr('data-popup-id');
        if(target){
            $('<div>'+ $('#'+target).html()+'</div>').dialog({title: elm.attr('title'),'width':550});
        }else {
            $.ajax({
                url: elm.attr('data-url'),
                success: function (data) {
                    $('<div>' + data.html + '</div>').dialog({title: data.title});
                }
            });
        }
    });
    lle.init_tabs();



});


var lle = {
    dialog: function(title,msg){
        $('<div>'+msg+'</div>').dialog({title:title});
    },

    init_tabs: function(){
        $( ".lle-tabs, .lle-tabs-v" ).tabs({
          beforeLoad: function( event, ui ) {
            ui.jqXHR.fail(function() {
              ui.panel.html( "une erreur est survenue" );
            });
          }
        });

        $( ".lle-tabs-v" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( ".lle-tabs-v li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
    },

    ajaxForm: function( $form, callback, type , elm, action){
      var values = {};
      var type = (type)? type:'json';
      var formdata = (window.FormData) ? new FormData($form[0]) : null;
      var data = (formdata !== null) ? formdata : $form.serialize();
      var action = (action)? action:$form.attr( 'action' );
      /*$.each( $form.serializeArray(), function(i, field) {
        values[field.name] = field.value;
      });*/
      $.ajax({
        type        : $form.attr( 'method' ),
        url         : action,
        contentType : false, // obligatoire pour de l'upload
        processData : false, // obligatoire pour de l'upload
        data        : data,
        dataType    : type,
        success     : function(data) {
          //var offset = $(field.id).offset().top
          //$('html, body').animate({scrollTop: offset}, 'slow');
          if(elm){
            callback( elm, data );
          }else{
            callback( data );
          }

        }
      });
    },

    ajax: function(elm){
        var type = (elm.attr('data-type'))? elm.attr('data-type'):'json';
        if(elm.attr('data-confirm')){
        if(!confirm(elm.attr('data-confirm'))) return;
        }
        var callback = window[elm.attr('data-callback')];
        var preCallback = (elm.attr('data-pre-callback'))? window[elm.attr('data-pre-callback')]:null;
        var errCallback = (elm.attr('data-err-callback'))? window[elm.attr('data-err-callback')]:null;
        if(preCallback){
            preCallback(elm);
        }
        var url = (elm.attr('data-url'))? elm.attr('data-url'):elm.attr('href');
        var name = (elm.attr('name'))? elm.attr('name'):null;
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
        name = (elm.attr('data-name'))? elm.attr('data-name'):elm.attr('name');
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

    filter_set_button: function(id, id_button, val) {

      $(id).val(val);
      $(id_button + ' .fa').hide();
      $(id_button + ' .'+val).show();
      return false;
    }
}

$.fn.extend({
    insertAtCaret: function(myValue){
      return this.each(function(i) {
        if (document.selection) {
          //For browsers like Internet Explorer
          this.focus();
          sel = document.selection.createRange();
          sel.text = myValue;
          this.focus();
        }
        else if (this.selectionStart || this.selectionStart == '0') {
          //For browsers like Firefox and Webkit based
          var startPos = this.selectionStart;
          var endPos = this.selectionEnd;
          var scrollTop = this.scrollTop;
          this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
          this.focus();
          this.selectionStart = startPos + myValue.length;
          this.selectionEnd = startPos + myValue.length;
          this.scrollTop = scrollTop;
        } else {
          this.value += myValue;
          this.focus();
        }
      })
    }
});
