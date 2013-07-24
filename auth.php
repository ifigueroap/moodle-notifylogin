<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Anobody can login with any password.
 *
 * @package auth_none
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');

function getNowDateStr() {
    $date = usergetdate(time());
    $nowTS = make_timestamp($date['year'], $date['mon'], $date['mday'], $date['hours'], $date['minutes'], $date['seconds']);
    $dateStr = date('d/m/Y H:i:s', $nowTS);
    return $dateStr;
}

/**
 * Plugin for no authentication.
 */
class auth_plugin_notifylogin extends auth_plugin_base {

    /**
     * Constructor.
     */
    function auth_plugin_notifylogin() {
        $this->authtype = 'notifylogin';
        $this->config = get_config('auth/notifylogin');
    }
    
    /**
     * Returns true if the username and password work or don't exist and false
     * if the user exists and the password is wrong.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     *
     * This plugin never authenticates successfully an user,
     * it always defer this to other auth plugin.
     */
    
    function user_login ($username, $password) {
      global $CFG;
      return false;
    }
    

    /**
     * Updates the user's password.
     *
     * called when the user password is updated.
     *
     * @param  object  $user        User table object
     * @param  string  $newpassword Plaintext password
     * @return boolean result
     *
     */
    /*
    function user_update_password($user, $newpassword) {
        $user = get_complete_user_data('id', $user->id);
        // This will also update the stored hash to the latest algorithm
        // if the existing hash is using an out-of-date algorithm (or the
        // legacy md5 algorithm).
        return update_internal_user_password($user, $newpassword);
    }
    */

    /*
    function prevent_local_passwords() {
        return false;
    }
    */

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal() {
        return true;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    /*
    function can_change_password() {
        return true;
    }
    */

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return moodle_url
     */
    /*
    function change_password_url() {
        return null;
    }
    */

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    /*
    function can_reset_password() {
        return true;
    }
    */

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $page An object containing all the data for this page.
     */
    function config_form($config, $err, $user_fields) {
        include "config.html";
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     */
    function process_config($config) {
        // set to defaults if undefined
        if (!isset($config->emails)) {
            $config->emails = '';
        }

        // Save settings.
        set_config('emails', $config->emails, 'auth/notifylogin');
        return true;
    }
    
    /**
     * Method called when a user has a sucessfull login in any authentication plugin.
     *
     * @return bool Always returns true.
     */
    public function user_authenticated_hook(&$user, $username, $password) {
      $emails = $this->config->emails;

      $dateStr = getNowDateStr();

      $to      = strval($emails);
      $subject = '[ ' . $dateStr . ']: ' . $user->firstname . ' ' . $user->lastname . ' ha iniciado sesión en el Aula Virtual';
      $message = 
	'El usuario '. $user->firstname . ' ' . $user->lastname . ' (' . $username . ') ' .
	'ha iniciado sesión en el Aula Virtual, a las: ' . $dateStr;
      $headers = 'From: noreply@alterorbis.cl' . "\r\n" .
                 'X-Mailer: PHP/' . phpversion();
      mail($to, $subject, $message, $headers);
      return true;
    }

    public function prelogout_hook() {
      global $USER, $COURSE;
      $emails = $this->config->emails;
      $user = $USER;
      $username = $USER->username;

      $dateStr = getNowDateStr();

      $to      = strval($emails);
      $subject =  '[ ' . $dateStr . ']: ' . $user->firstname . ' ' . $user->lastname . ' ha cerrado sesión en el Aula Virtual';
      $message = 
	'El usuario '. $user->firstname . ' ' . $user->lastname . ' (' . $username . ') ' .
	'ha cerrado sesión en el Aula Virtual, a las: ' . $dateStr;
      $headers = 'From: noreply@alterorbis.cl' . "\r\n" .
                 'X-Mailer: PHP/' . phpversion();
      mail($to, $subject, $message, $headers);
    }
}


