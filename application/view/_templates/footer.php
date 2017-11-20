        

	
	<?php 
    $this_page = strtolower(str_ireplace('.php', '', basename($_SERVER['QUERY_STRING'])));  

	// simply adds the scripts required for the selected page view
    switch ($this_page){
		case 'viewweather':
		case 'viewpaddock':
		case 'survey':
		//case 'createpaddock':
		case 'paddock':
		case 'draw':	
/*			echo '<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWS8UlXRrp5LWpuTZwWIncbTscg_-55hs &libraries=drawing,geometry,places&callback=initMap"></script>';*/
			echo '<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAbQt9IOLVTvVZPOSirHKPJ-SmxJ16huic &libraries=drawing,geometry,places&callback=initMap"></script>';
			break;	
		default:       
    } ?>
  
	<div class="footer_container">
		<div class="footer">&copy 2016-<?php echo date('Y'); ?> Onions New Zealand. All Rights Reserved. <a href="<?php echo Config::get('URL'); ?>index/terms">Terms & Conditions</a></div>
		<!--<div class="footer" style="float:right;text-align:right;padding-right:5px;">Site by <a href="http://www.justsimplifyit.nz">JUST simplify IT</a></div>-->
	</div>
	</div><!-- close class="wrapper" -->
</body>
</html>