<?php   
	/**
	 * * load plugin's core functionality :)
	 */
	class kmc_init
	{
		//load depencies
		public function depencies(){   
 			require_once( TPRM_MEM_CO_CLASS . "helper-class.php" ); 
 			require_once( TPRM_MEM_CO_CLASS . "shortcode-class.php" ); 
			require_once( TPRM_MEM_CO_CLASS . "main-class.php" );
			require_once( TPRM_MEM_CO_CLASS . "email-class.php" );			
			require_once( TPRM_MEM_CO_CLASS . "lock-account-class.php" );			
		}

		//initialize classes
		public function classes(){
			new TPRM_membership_helper();
			new TPRM_membership_coupon();
			new TPRM_add_to_cart_shortcode();
			new TPRM_membership_shortcode();
			new TPRM_membership_email();
			new TPRM_lock_account();
		}

		public function run(){		
			self::depencies();			
			self::classes();
		} 
	}