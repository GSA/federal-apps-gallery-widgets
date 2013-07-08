jQuery(document).ready(function( $ ) {

    var $jq=jQuery.noConflict();$jq(document).ready(function(){$jq(".updated").fadeIn(1000).fadeTo(1000,1).fadeOut(1000);
    $jq('.click').click(function(){var id=$jq(this).attr('id');$jq('#box'+id).slideToggle('slow',function(){})})
    });

    $jq('#borderColor').ColorPicker({onShow:function(colpkr){$jq(colpkr).fadeIn(500);return false},onHide:function(colpkr){$jq(colpkr).fadeOut(500);return false},onSubmit:function(hsb,hex,rgb,el){$jq(el).val(hex);$jq(el).ColorPickerHide()},onBeforeShow:function(){$jq(this).ColorPickerSetColor(this.value)}}).bind('keyup',function(){$jq(this).ColorPickerSetColor(this.value)});
    $(".submit_update").click(function(e) {
        e.preventDefault();
        option_name = document.getElementsByName("page_options")[0].value;
        original_rows = document.getElementsByName("original_rows")[0].value;
        new_rows = $('input:text[name='+option_name+'[numrows]]').val();

        if(original_rows < new_rows)
        {
            for(x=parseInt(original_rows)+1;x<=new_rows;x++)
            {
                $("#appsgal_form").append($('<input/>').attr('type', 'hidden').attr('name', (option_name+'[appsperrow'+x+']')).val('7'));
                $("#appsgal_form").append($('<input/>').attr('type', 'hidden').attr('name', (option_name+'[num_visible'+x+']')).val('7'));
                $("#appsgal_form").append($('<input/>').attr('type', 'hidden').attr('name', (option_name+'[movement_speed'+x+']')).val('4000'));
                $("#appsgal_form").append($('<input/>').attr('type', 'hidden').attr('name', (option_name+'[delay'+x+']')).val('0'));
                if(x%2==1)
                    $("#appsgal_form").append($('<input/>').attr('type', 'hidden').attr('name', (option_name+'[scroll_forward'+x+']')).val('true'));
                else
                    $("#appsgal_form").append($('<input/>').attr('type', 'hidden').attr('name', (option_name+'[scroll_forward'+x+']')).val('false'));
                $("#appsgal_form").append($('<input/>').attr('type', 'hidden').attr('name', (option_name+'[borderColor'+x+']')).val(''));
                $("#appsgal_form").append($('<input/>').attr('type', 'hidden').attr('name', (option_name+'[iconheight'+x+']')).val('100'));
                $("#appsgal_form").append($('<input/>').attr('type', 'hidden').attr('name', (option_name+'[iconwidth'+x+']')).val('80'));
                $("#appsgal_form").append($('<input/>').attr('type', 'hidden').attr('name', (option_name+'[hover_pause'+x+']')).val('true'));
                $("#appsgal_form").append($('<input/>').attr('type', 'hidden').attr('name', (option_name+'[auto-play'+x+']')).val('true'));
            }
        }
        $("#appsgal_form").submit();
    });

    $("#submit_refresh").click(function(e) {
        e.preventDefault();
        if(e.target.id == 'submit_refresh') {
            
            $("#appsgal_form").append($('<input/>').attr('type', 'hidden').attr('name', 'refresh_api').val('1'));
            api_url = $("#appsgal_api").val()+($("#appsgal_api").val().indexOf("?") !== -1 ? '&_callback=?' : '?_callback=?');
                
            $.ajax({ 
                type: 'GET',
                //http://localhost/apps-gallery/api/registrations.json
                //url: 'http://apps.usa.gov/apps-gallery/api/registrations.json?callback=?',
                url: api_url,
                dataType: 'jsonp',
                jsonpCallback: "FOOO",
                success: function(data) { 
                    option_name = document.getElementsByName("page_options")[0].value;

                    var i = 0;
                    if(data.hasOwnProperty('results'))
                    {
                        $.each(data['results'],function(index){
                        
                        $("#appsgal_form").append($('<input/>').attr('type', 'hidden').attr('name', (option_name+'[appgal_img'+(index+1)+']')).val(this['Icon'][0]));
                        $("#appsgal_form").append($('<input/>').attr('type', 'hidden').attr('name', (option_name+'[appgal_link'+(index+1)+']')).val(this['Friendly_Url']));
                        $("#appsgal_form").append($('<input/>').attr('type', 'hidden').attr('name', (option_name+'[appgal_title'+(index+1)+']')).val(this['Name']));
                        });
                        $("#appsgal_form").append($('<input/>').attr('type', 'hidden').attr('name', (option_name+'[totalapps]')).val(data['results'].length));
                        
                    }
                    //console.log(data);
                    //alert('api success');
                    $("#appsgal_form").submit();
                }
                });
        }

    });

    $("#target").click(function() {
      $("#target2").text('visible' +window.visibleCollection);
      $("#target3").text('hidden: '+window.hiddenCollection);
      //console.log(window.visibleCollection);
    });

});