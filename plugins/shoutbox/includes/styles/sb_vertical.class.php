<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-05-30 23:41:49 +0200 (Mi, 30. Mai 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 11795 $
 *
 * $Id: sb_vertical.class.php 11795 2012-05-30 21:41:49Z wallenium $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists('sb_style_base'))
{
  include_once(registry::get_const('root_path').'plugins/shoutbox/includes/styles/sb_style_base.class.php');
}

/*+----------------------------------------------------------------------------
  | sb_vertical
  +--------------------------------------------------------------------------*/
if (!class_exists("sb_vertical"))
{
  class sb_vertical extends sb_style_base
  {
    /**
     * __dependencies
     * Get module dependencies
     */
    public static function __shortcuts()
    {
      $shortcuts = array('user', 'config', 'pdh', 'html');
      return array_merge(parent::$shortcuts, $shortcuts);
    }

    /**
     * layoutShoutbox
     * get the complete shoutbox layout
     *
     * @return  string
     */
    protected function layoutShoutbox()
    {
      // default is empty output
      $htmlOut = '';

      // get location of form
      $form_location = ($this->config->get('sb_input_box_location') != '') ? $this->config->get('sb_input_box_location') : 'top';

      // is input on top (and user can add entries) append form first
      if ($form_location == 'top' && $this->user->check_auth('u_shoutbox_add', false))
      {
        $htmlOut .= $this->getForm();
      }

      // content table
      $htmlOut .= '<div id="htmlShoutboxTable">';
      $htmlOut .= $this->getContent();
      $htmlOut .= '</div>';

      // archive link? (User must be logged in to see archive link)
      if ($this->config->get('sb_show_archive') && $this->user->is_signedin())
      {
        $htmlOut .= $this->getArchiveLink();
      }

      // is input below (and user can add entries) append form
      if ($form_location == 'bottom' && $this->user->check_auth('u_shoutbox_add', false))
      {
        $htmlOut .= $this->getForm();
      }

      return $htmlOut;
    }

    /**
     * layoutContent
     * layout the content only of the shoutbox
     *
     * @param  string  $root_path  root path
     *
     * @return  string
     */
    protected function layoutContent($root_path)
    {
      // get location of form
      $form_location = ($this->config->get('sb_input_box_location') != '') ? $this->config->get('sb_input_box_location') : 'top';

      // empty output
      $htmlOut = '';

      // display
      if (is_array($this->shoutbox_ids) && count($this->shoutbox_ids) > 0 && is_dir($root_path))
      {
        // output table header
        $htmlOut .= '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="colorswitch hoverrows">';

        // input above? If true, insert a space row
        if ($form_location == 'top' && $this->user->check_auth('u_shoutbox_add', false))
          $htmlOut .= '<tr><th>&nbsp;</th></tr>';

        // output
        foreach ($this->shoutbox_ids as $shoutbox_id)
        {

          $htmlOut .= '<tr>
                         <td>';

          // if admin or own entry, ouput delete link
          if ($this->user->data['user_id'] == $this->pdh->get('shoutbox', 'userid', array($shoutbox_id)) ||
              $this->user->check_auth('a_shoutbox_delete', false))
          {
            $img = $root_path.'images/global/delete.png';

            // Java Script for delete
            $htmlOut .= '<span class="small bold floatRight hand" onclick="$(\'#del_shoutbox\').ajaxSubmit(
                           {
                             target: \'#htmlShoutboxTable\',
                             url:\''.$root_path.'plugins/shoutbox/shoutbox.php'.$this->SID.'&amp;sb_delete='.$shoutbox_id.'&amp;sb_root='.rawurlencode($root_path).'&amp;sb_orientation=vertical\',
                             beforeSubmit: function(formData, jqForm, options) {
                               deleteShoutboxRequest(\''.$root_path.'\', '.$shoutbox_id.', \''.$this->user->lang('delete').'\');
                             }
                           }); ">
                           <span id="shoutbox_delete_button_'.$shoutbox_id.'">
                             <img src="'.$img.'" alt="'.$this->user->lang('delete').'" title="'.$this->user->lang('delete').'"/>
                           </span>
                         </span>';
          }

          // output date as well as User and text
          $htmlOut .= $this->pdh->geth('shoutbox', 'date', array($shoutbox_id, $this->config->get('sb_show_date'))).
                      '<br/>'.
                      $this->pdh->geth('shoutbox', 'usermembername', array($shoutbox_id)).
                      ':<br/>'.
                      $this->pdh->geth('shoutbox', 'text', array($shoutbox_id, $root_path));

          $htmlOut .= '  </td>
                       </tr>';
        }

        // output table footer
        $htmlOut .= '</table>';
      }
      else
      {
        $htmlOut .= '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="colorswitch">
                       <tr>
                         <td><div class="center">'.$this->user->lang('sb_no_entries').'</div></td>
                       </tr>
                     </table>';
      }

      return $htmlOut;
    }

    /**
     * jCodeOrientation
     * get the orientation for the JCode output
     *
     * @return  string
     */
    protected function jCodeOrientation()
    {
      return 'vertical';
    }

    /**
     * getForm
     * get the Shoutbox <form>
     *
     * @param  string  $rpath  root path
     *
     * @return  string
     */
    private function getForm($rpath='')
    {
      // root path
      $root_path = ($rpath != '') ? $rpath : $this->root_path;

      // get location and max text length
      $form_location = ($this->config->get('sb_input_box_location') != '') ? $this->config->get('sb_input_box_location') : 'top';

      // only display form if user has members assigned to or if user modus is selected
      $members = $this->pdh->get('member', 'connection_id', array($this->user->data['user_id']));
      if ((is_array($members) && count($members) > 0) ||
          $this->config->get('sb_use_users', 'shoutbox'))
      {
        // html
        $out = '<form id="reload_shoutbox" name="reload_shoutbox" action="'.$root_path.'plugins/shoutbox/shoutbox.php" method="post">
                </form>
                <form id="Shoutbox" name="Shoutbox" action="'.$root_path.'plugins/shoutbox/shoutbox.php" method="post">
                  <table width="100%" border="0" cellspacing="1" cellpadding="2" class="colorswitch">';

        // input below? If true insert space row
        if ($form_location == 'bottom' && $this->user->check_auth('u_shoutbox_add', false))
        {
          $out .= '<tr><th>&nbsp;</th></tr>';
        }

        $out .= '<tr>
                   <td>
                     <div class="center">'
                     .$this->getFormName().
                    '</div>
                   </td>
                 </tr>
                 <tr>
                   <td>
                     <div class="center">
                       <textarea class="input" name="sb_text" style="width: 90%;" rows="3" cols="1"></textarea>
                     </div>
                   </td>
                 </tr>
                 <tr>
                   <td>
                     <div class="center">
                       <input type="hidden" name="sb_root" value="'.urlencode($root_path).'"/>
                       <input type="hidden" name="sb_orientation" value="vertical"/>
                       <span id="shoutbox_button"><input type="submit" class="mainoption bi_ok" name="sb_submit" value="'.$this->user->lang('sb_submit_text').'"/></span>
                       <span class="small bold hand" onclick="$(\'#reload_shoutbox\').ajaxSubmit(
                         {
                           target: \'#htmlShoutboxTable\',
                           url:\''.$root_path.'plugins/shoutbox/shoutbox.php'.$this->SID.'&amp;sb_root='.rawurlencode($root_path).'&amp;sb_orientation=vertical\',
                           beforeSubmit: function(formData, jqForm, options) {
                             reloadShoutboxRequest(\''.$root_path.'\');
                           },
                           success: function() {
                             reloadShoutboxFinished(\''.$root_path.'\', \''.$this->user->lang('sb_reload').'\');
                           }
                         });">
                         <span id="shoutbox_reload_button">
                           <img src="'.$root_path.'plugins/shoutbox/images/reload.png" alt="'.$this->user->lang('sb_reload').'" title="'.$this->user->lang('sb_reload').'"/>
                         </span>
                       </span>
                     </div>
                   </td>
                 </tr>
               </table>
             </form>';
      }
      else if ($this->config->get('sb_use_users', 'shoutbox'))
      {
        $out .= '<div class="center">'.$this->user->lang('sb_no_character_assigned').'</div>';
      }

      return $out;
    }

    /**
     * getFormName
     * get the Shoutbox <form> Names
     *
     * @return  string
     */
    private function getFormName()
    {
      // for anonymous user, just return empty string
      $outHtml = '';

      // if we have users, just return the single user, otherwise use member dropdown
      if ($this->config->get('sb_use_users', 'shoutbox'))
      {
        // show name as text and user id as hidden value
        $username = $this->pdh->get('user', 'name', array($this->user->data['user_id']));
        $outHtml .= '<input type="hidden" name="sb_usermember_id" value="'.$this->user->data['user_id'].'"/>'.$username;
      }
      else
      {
        // get member array
        $members = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'connection_id', array($this->user->data['user_id']))));
        if (is_array($members))
        {
          $membercount = count($members);

          // if more than 1 member, show dropdown box
          if ($membercount > 1)
          {
            // show dropdown box
            $outHtml .= $this->html->DropDown('sb_usermember_id', $members, $this->pdh->get('user', 'mainchar', array($this->user->id)));
          }
          // if only one member, show just member
          else if ($membercount == 1)
          {
            // show name as text and member id as hidden value
            $outHtml .= '<input type="hidden" name="sb_usermember_id" value="'.key($members).'"/>'.
                        current($members);
          }
        }
      }

      return $outHtml;
    }

    /**
     * getArchiveLink
     * get the archive link text
     *
     * @return  string
     */
    private function getArchiveLink()
    {
      $html = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="colorswitch">
                 <tr>
                   <td class="menu">
                     <div class="center">
                       <input type="button" class="liteoption bi_archive" value="'.$this->user->lang('sb_archive').'" onclick="window.location.href=\''.$this->root_path.'plugins/shoutbox/archive.php'.$this->SID.'\'"/>
                     </div>
                   </td>
                 </tr>
               </table>';

      return $html;
    }

  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_sb_vertical', sb_vertical::__shortcuts());
?>
