<?php
class SocialRegistration extends Plugin
{
	/*
	 * Handle the result when a user identified himself
	 */
	public function action_socialauth_identified( $service, $userdata, $state = '' )
	{
		if(preg_match('%loginform_registration%', $state)) {
			// $group = UserGroup::get($form->get_option('group_name'));
			$user = new User( array( 'username' => $userdata['email'], 'email' => $userdata['email'] ) );
			if ($user->insert()) {
				// $group->add($user);
				$user->portrait_url = $userdata['portrait_url'];
				$user->info->displayname = $userdata['name'];
				$fieldname = "servicelink_$service";
				$user->info->{$fieldname} = $userdata['id'];
				$user->update();
				Session::notice(_t('An account has been created for you. Your username is your email address.'));
				Utils::redirect(Site::get_url('login'));
			}
			else {
				Session::error(_t('Your account could not be created'));
				Utils::redirect(Site::get_url('login'));
			}
		}
	}
	
	/**
	 * Add the login link to the login form
	**/
	public function action_form_login($form)
	{
		$services = Plugins::filter( 'socialauth_services', array() );
		$html = '';
		foreach( $services as $service ) {
			$html .= '<p><a href="' . $form->get_theme()->socialauth_link($service, array('state' => 'loginform_registration')) . '">' . _t( 'Register with %s', array ( $service ), __CLASS__ ) . '</a></p>';
		}
		$form->append('static', 'socialadmin', $html);
	}
	
	/*
	 * Habari 0.9 style form editing, does the same as the above function
	 */
	public function action_theme_loginform_controls()
	{
		$services = Plugins::filter( 'socialauth_services', array() );
		$html = '';
		$theme = Themes::create();
		foreach( $services as $service ) {
			$html .= '<p><a href="' . $theme->socialauth_link($service, array('state' => 'loginform_registration')) . '">' . _t( 'Register with %s', array ( $service ), __CLASS__ ) . '</a></p>';
		}
		echo $html;
	}
}
?>