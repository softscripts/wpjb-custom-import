<?php 
/***************************
 Get url
 :- Args: Data String
****************************/
function jci_get_url($page) {
	if($page) {
		return get_admin_url(get_current_blog_id(),'admin.php?page=jci_'.$page);
	}
}

/**********************
 Go to step link
 :- Args: Data Number
**********************/
function jci_go_to_step($step) {
	if($step) {
		$url = jci_get_url('import').'&step='.$step;
		return $url;
	}
}

/********************************
 Remove single quote from string
 :- Args: String
*********************************/
function jci_removesinglequote($string) {
	if($string) {
		return str_replace("'","",$string);
	}
}

/********************************
 Debug String/Array
 :- Args: Label, Value
*********************************/
function jci_debug($label, $data) {
	echo "<div style=\"margin-left: 40px;background-color:#eeeeee;\"><u><h3>".$label."</h3></u><pre style=\"border-left:2px solid #000000;margin:10px;padding:4px;\">".print_r($data, true)."</pre></div>";
}

/********************************
 IS ARRAY ASSOCIATIVE
 :- Args: ARRAY
*********************************/
function jci_isAssoc($array)
{
    $array = array_keys($array); return ($array !== array_keys($array));
}

/********************************
 XML to ARRAY
 :- Args: XML Object
*********************************/
function jci_xml2array ( $xmlObject, $out = array () ){
    foreach ( (array) $xmlObject as $index => $node )
        $out[$index] = ( is_object ( $node ) ) ? jci_xml2array ( $node ) : $node;
    	return $out;
}


/***************************
 Process XML file (STEP 1)
 :- Args: XML FILE
***************************/
function jci_import_xml($xml,$pwd) {	
	$jci_jobs = simplexml_load_file($xml); //Import XML to Array
	$final_xml_array = array();
	$all_jobs = array();
	//jci_debug('XML',$jci_jobs->job);
	if(count($jci_jobs) > 0) {		
		$xml_array = jci_xml2array($jci_jobs);
		if(jci_isAssoc($xml_array['job'])) {
			$xml_jobs[0] = $jci_jobs->job;
		}		
		else{
			$xml_jobs = $xml_array['job'];
		}		
		//jci_debug('Jobs',$xml_jobs);		
		foreach($xml_jobs as $xml_job){
			$job = array();
			$job['company_name'] 			= sanitize_text_field($xml_job->company);
			$job['job_title'] 				= sanitize_text_field($xml_job->title);
			$job['job_slug'] 				= sanitize_title($xml_job->title);
			$job['job_description'] 		= $xml_job->description;
			$job['job_country'] 			= sanitize_text_field($xml_job->country);
			$job['job_state'] 				= sanitize_text_field($xml_job->state);
			$job['job_zip_code'] 			= sanitize_text_field($xml_job->postalcode);
			$job['job_city'] 				= sanitize_text_field($xml_job->city);
			$job['job_created_at'] 			= sanitize_text_field($xml_job->date);
			$job['job_modified_at'] 		= sanitize_text_field($xml_job->date);
			$job['is_active'] 				= 1;
			$job['is_approved'] 			= 1;
			$job['is_filled'] 				= 0;
			$job['is_featured'] 			= 0;
			$job['jobtype']					= sanitize_text_field($xml_job->jobtype);
			$job['tags']['tag']['type']		= 'category';
			$job['tags']['tag']['title']	= sanitize_text_field($xml_job->category);
			$job['tags']['tag']['slug']		= sanitize_title($xml_job->category);
			$job['metas']['meta']['name']	= 'job_description_format';
			$job['metas']['meta']['value']	= 'html';
			
			array_push($all_jobs, $job);
		}
		$final_xml_array['jobs'] = $all_jobs;
			
		//jci_debug('Jobs',$final_xml_array);
		
		$xml = new SimpleXMLElement("<?xml version=\"1.0\"?><wpjb></wpjb>");
		$jobs = $xml->addChild('jobs');			
		foreach($all_jobs as $job_array) {
			$job = $jobs->addChild('job');
			$job->company_name 		= $job_array['company_name'];
			$job->job_title 		= $job_array['job_title'];
			$job->job_slug			= $job_array['job_slug'];
			$job->job_description 	= $job_array['job_description'];
			$job->job_country		= $job_array['job_country'];
			$job->job_state			= $job_array['job_state'];
			$job->job_zip_code		= $job_array['job_zip_code'];
			$job->job_city			= $job_array['job_city'];
			$job->job_created_at	= $job_array['job_created_at'];
			$job->job_modified_at	= $job_array['job_modified_at'];
			$job->is_active			= $job_array['is_active'];
			$job->is_approved		= $job_array['is_approved'];
			$job->is_filled			= $job_array['is_filled'];
			$job->is_featured		= $job_array['is_featured'];
			$tags 					= $job->addChild('tags');
			$tag 					= $tags->addChild('tag');
			$tag->type				= $job_array['tags']['tag']['type'];
			$tag->title 			= $job_array['tags']['tag']['title'];
			$tag->slug				= $job_array['tags']['tag']['slug'];
			$metas 					= $job->addChild('metas');
			$meta 					= $metas->addChild('meta');
			$meta->name				= $job_array['metas']['meta']['name'];
			$meta->value			= $job_array['metas']['meta']['value'];
			
		}
		$xml_var = $xml->asXML();
		//$xml->saveXML(ABSPATH.'xml/final.xml');
		
		//echo $xml_var;
		
		$site = site_url();
		$current_user = wp_get_current_user();
		$username = $current_user->user_login;
		$password = $pwd;
	
		// import setup
		$path = 'wp-admin/admin-ajax.php';
		$url = admin_url('admin-ajax.php');
		$data = array(
		 'username' => $username,
		 'password' => $password,
		 'xml' => $xml_var,
		 'action' => 'wpjb_import_api'
		);

		$options = array(
		 'http' => array(
		 'header' => "Content-type: application/x-www-form-urlencoded",
		 'method' => 'POST',
		 'content' => http_build_query($data),
		 ),
		);

		$context = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		$result = simplexml_load_string($result);
		
		return $result;
		
	}
}

?>
