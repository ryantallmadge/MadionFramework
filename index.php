<?php
/**
 * Madi-on Framework
 * @author		Ryan Tallmadge
 * @date		07/14/2011
 * @version		1.0
 * @copyright   2011
 */

//Look for www in URL if no there redirect to it...
//@TODO Remove the comments below to force the www in the URL
//$WWW_URL = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
//if(strpos($WWW_URL, 'www')===false){header("Location: http://www.".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);exit;}

/*
 *---------------------------------------------------------------
 * SYSTEM CONSTANT PARAMS
 *---------------------------------------------------------------
 *
 * For the scalability of the project and for being able to move things around
 * lets define the system constants so we always know where things are.
 */
 
    // Environment for error loading and checking
    define('ENVIRONMENT', 'development');
	
	// Path to the root folder
	define('BASEPATH', $_SERVER["DOCUMENT_ROOT"]);
	
	// Path to the core dir / classes that make the site run example: (database class, session class, etc...)
	define('COREDIR', BASEPATH . '/core');

    // Application folder / Controllers/Modles
    define('APPPATH', BASEPATH . '/application');
    
	// Application folder / Controllers/Modles
    define('VIEWPATH', BASEPATH . '/application/views');
	
	// The PHP file extension
	define('EXT', '.php');
	

    
/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */

if (defined('ENVIRONMENT'))
{
	switch (ENVIRONMENT)
	{
		case 'development':
			error_reporting(-1);
			ini_set('display_errors', '1');
			require_once COREDIR.'/debug'.EXT;
            $starttimer = new Timer;
            $starttimer->start();
		break;
	
		case 'testing':
		case 'production':
			error_reporting(0);
		break;

		default:
			exit('The application environment is not set correctly.');
	}
}



/*
 * --------------------------------------------------------------------
 * LOAD THE CONFIG FILE
 * --------------------------------------------------------------------
 * 
 */
	//Load the main config code
	require_once COREDIR.'/configs/main_config'.EXT;
	

/*
 * --------------------------------------------------------------------
 * LOAD GLOBAL HELPER FILES - CORE FUNCTIONS
 * --------------------------------------------------------------------
 * 
 */
 	//Load our flash session code
	require_once COREDIR.'/helpers/flashmessage'.EXT;
 	//Load our flash session code
	require_once COREDIR.'/helpers/cleancheck'.EXT;

/*
 *---------------------------------------------------------------
 * LOAD BASE CONTROLLER
 *---------------------------------------------------------------
 * All of our controllers will extend this class
 */
	require_once COREDIR.'/basecontroller'.EXT;
/*
 *---------------------------------------------------------------
 * LOAD BASE MODEL
 *---------------------------------------------------------------
 * All of our model will extend this class
 */
	require_once COREDIR.'/basemodel'.EXT;
/*
 * --------------------------------------------------------------------
 * LOAD LAUNCHER FILE
 * --------------------------------------------------------------------
 *
 *
 */
	//Load the launcher file....3....2.....1.....Lift off
	require_once COREDIR.'/launch'.EXT;





//################################DEBUG STUFF##########################################
//This will be turned off when setting the enviroment above (line: 22) to production....
if(class_exists('Timer')){ 
	echo "\n\n<!-- \n\n#####################DEBUG STUFF#############################\n\n";
	echo "URI_PARTS: ";           print_r($route->url);                     echo"\n\n";
	echo "SESSION: ";             print_r($_SESSION);                        echo"\n\n";
	echo "REQUEST: ";             var_dump($boot->_REQUEST_);                echo"\n\n";
	echo "CONTROLLER: ";          echo $load_class;                             echo"\n\n";
	echo "METHOD: ";              echo $load_method;                             echo"\n\n";
	echo "BASE URL: ";            echo BASE_URL;                             echo"\n\n";
	echo "IMAGES URL: ";          echo IMAGES_URL;                           echo"\n\n";
	echo "GEO LOCATE IP:";        echo $_SESSION['GEOLOCATE']['ip'];         echo"\n\n";
	echo "GEO LOCATE City:";      echo $_SESSION['GEOLOCATE']['city'];       echo"\n\n";
	echo "GEO LOCATE Longitude:"; echo $_SESSION['GEOLOCATE']['longitude'];  echo"\n\n";
	echo "GEO LOCATE Latitude:";  echo $_SESSION['GEOLOCATE']['latitude'];   echo"\n\n";
	echo "GEO LOCATE Area Code:"; echo $_SESSION['GEOLOCATE']['areaCode'];   echo"\n\n";
	echo "GEO LOCATE Region:";    echo $_SESSION['GEOLOCATE']['region'];     echo"\n\n";	
	echo "GEO LOCATE Zip Code:";  echo $_SESSION['GEOLOCATE']['zipcode'];    echo"\n\n";	
	//echo "VARS TO PAGE:";         print_r($boot->_VIEW_->view_params);       echo"\n\n";	
	echo "PHP ran in: ";          echo $starttimer->stop();                  echo" seconds\n\n-->\n\n";
}


/* End of file index.php */
/* Location: ./index.php */