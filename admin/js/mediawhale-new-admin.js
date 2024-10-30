jQuery(document).ready(function($){
	/*
	Comment until world is 100% fixed;
	jQuery(document.body).on( "change", "#display_player_callback",function(){
		if(jQuery(this).val() == 'after_title' || jQuery(this).val() == 'before_title'){
			jQuery(".mediawhale-warning-txt").show();
		}
		else{
			jQuery(".mediawhale-warning-txt").hide();
		}
	});
	*/

    jQuery('select#language_name option[value="'+$("#language_name").attr("data-selected")+'"]').attr('selected','selected');
        $(document.body).on("change",".language_name",function(event){
            $("select#voice_name").html('<option>Loading...</option>');
            $.ajax({
                url: 'https://app.mediawhale.com/wp-admin/admin-ajax.php',
                data: {
                    'action': 'get_voices_from_lang_ajax_request',
                    'langid' : $(this).val(),
                },
                success:function(data) {
                    $("select#voice_name").html(data);
                    jQuery('#voice_name option[value="'+jQuery("#voice_name").attr("data-selected")+'"]').attr("selected","selected");
                    $("select#voice_name").trigger("change");

                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
            });         
        });

        $(".language_name").trigger("change"); 
    
    $('#specific_posts_0').select2();

    $(document.body).on("click","#convert_box_m",function(event){
        $.ajax({
            url: mediawhale_ajax.ajax_url,
            data: {
                'action': 'mediawhale_all_converter_ajax_request',
                'nonce' : mediawhale_ajax.nonce
            },
            success:function(data) {
                window.location.reload();
            },
            error: function(errorThrown){
                console.log(errorThrown);
            }
        });         
    });

    $(document.body).on("click",".convert_these_posts",function(event){

        $(".loading-center-convert").show();
        $(".convert_selector").hide();

        $.ajax({
            url: mediawhale_ajax.ajax_url,
            data: {
                'action': 'mediawhale_select_converter_ajax_request',
                'nonce' : mediawhale_ajax.nonce,
                'post_ids': $("#specific_posts_0").val()
            },
            success:function(data) {
                $(".convert_selector").show();
                $(".loading-center-convert").hide();
                $(".success-message-block").show();
            },
            error: function(errorThrown){
                console.log(errorThrown);
            }
        });         
    });

    $(".listen-voice-sample").click(function(){

        $("#preview_audio audio").get(0).play();

    });

    $("select#voice_name").change(function(){
        $("a.listen-voice-sample svg").css("opacity","0");
        $("a.listen-voice-sample span").html('Loading Sample...');
        $.ajax({
            url: 'https://app.mediawhale.com/wp-admin/admin-ajax.php?action=get_voices_sample_link&langid='+$(this).val(),
            success:function(data) {
                $("a.listen-voice-sample svg").css("opacity","1");
                $("#preview_audio").html('<audio controls ><source src="'+data+'" type="audio/mpeg"></audio>');
                $("a.listen-voice-sample span").html('Listen Voice Sample');
            },
            error: function(errorThrown){
                console.log(errorThrown);
            }
        });  


    });

    $(document.body).on("click","#stop_convert",function(event){
        $.ajax({
            url: mediawhale_ajax.ajax_url,
            data: {
                'action': 'mediawhale_stop_converter_ajax_request',
                'nonce' : mediawhale_ajax.nonce
            },
            success:function(data) {
                window.location.reload();
            },
            error: function(errorThrown){
                console.log(errorThrown);
            }
        });         
    });

    $(".convert_first_option").click(function(){
        $(".converter_give_options").slideUp();
        $(".converter_box_m_block").slideDown();
    });

    $(".convert_specific_posts").click(function(){
        window.location = $(this).attr("data-link") + "admin.php?page=convert-specific-posts";
    });

    $(".language_name").trigger("change");

});
