// JavaScript Document

function mapa_show_hide_settings()
{
	if( jQuery( 'input[name="mapa_settings[mapa_enable_master_password]"]' ).is(":checked")  )
	{
		jQuery( 'input[name="mapa_settings[mapa_master_password_is_admin_password]"]' ).closest("tr").show();
		
		jQuery( 'input[name="mapa_settings[mapa_can_admin_user_access]"]' ).closest("tr").show();
				
		jQuery( 'input[name="mapa_settings[mapa_master_password]"]' ).closest("tr").show();
	}
	else
	{
		jQuery( 'input[name="mapa_settings[mapa_master_password_is_admin_password]"]' ).closest("tr").hide();
		
		jQuery( 'input[name="mapa_settings[mapa_can_admin_user_access]"]' ).closest("tr").hide();
				
		jQuery( 'input[name="mapa_settings[mapa_master_password]"]' ).closest("tr").hide();	
		
	}
}
jQuery(function() {    
	mapa_show_hide_settings();
	jQuery( 'input[name="mapa_settings[mapa_enable_master_password]"]' ).change(function(){
		mapa_show_hide_settings();	
	});	
});