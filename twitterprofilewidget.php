<?php

	
if (!defined('_PS_VERSION_'))
	exit;
	
class twitterprofilewidget extends Module {
	private $configVars = array();
	
	
	
	public function __construct() {
		$this->name = 'twitterprofilewidget';				
		$this->tab = 'front_office_features';		
		$this->version = '1.0';
		$this->author = 'Sergio Carracedo (www.sergiocarracedo.es)';
		
		
		$this->configVars=array(
			"username" 		=> array(
									'title' 	=> $this->l('Twitter username'),
									'default' 	=> 'opsou'
								),
			
			"width"			=> array(
									'title' 	=> $this->l('Width'),
									'default' 	=> 210
								),
			"height"		=> array(
									'title' 	=> $this->l('Height'),
									'default' 	=> 300
								),
			"nbtweets"		=> array(
									'title' 	=> $this->l('Number of tweets'),
									'default' 	=> '4'
								),
			
			"shellbackground"	=> array(
									'title' 	=> $this->l('Shell background color'),
									'default' 	=> '#333333'									
								),
							
			"shelltext"		=> array(
									'title' 	=> $this->l('Shell text color'),
									'default' 	=> '#ffffff'
								),
			"tweetbackground"	=> array(
									'title' 	=> $this->l('Tweet background color'),
									'default' 	=> '#000000'
								),
			"tweettext"		=> array(
									'title' 	=> $this->l('Tweet text color'),
									'default' 	=> '#ffffff'
								),
			"links"		=> array(
									'title' 	=> $this->l('Links color'),
									'default' 	=> '#4aed05'
								)	
		);
		
		
		parent::__construct();

		$this->displayName = $this->l('Twitter profile widget');
		$this->description = $this->l('Add a Twitter profile widget Block');
	}
	
	public function install() {
		$res=parent::install();
		
		foreach ($this->configVars as $varName => $var) {
			$res = $res &&  Configuration::updateValue($this->name."_".$varName,$var["default"]);			
		}			

		$res= $res && $this->registerHook('header') 
			&& $this->registerHook('rightColumn') 
			&& $this->registerHook('leftColumn');
		
		return $res;
		
	}
	
	public function uninstall() {
		$res=true;		
		foreach ($this->configVars as $varName => $var) {
			$res = $res &&  Configuration::deleteByName($this->name."_".$varName);			
		}			
		
		return $res &&  parent::uninstall();
		
	}
	
	public function getContent() {
		$html = '';
		// If we try to update the settings
		if (isset($_POST['submitModule']))  {
			
			foreach ($this->configVars as $varName => $var) {
				$value= ((isset($_POST[$this->name."_".$varName])  ? $_POST[$this->name."_".$varName] : ''));
				
				Configuration::updateValue($this->name."_".$varName,$value);			
			}		
			
			$html .= '<div class="confirm">'.$this->l('Configuration updated').'</div>';
		}

		$html .= '
		<h2>'.$this->displayName.'</h2>
		<form action="'.Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>';
			foreach ($this->configVars as $varName => $var) {
				$fullVarName=$this->name."_".$varName;	
				if (empty($var["values"])) {
					$html.='<p><label for="'.$varName.'">'.$var["title"].' :</label>
					<input type="text" id="'.$fullVarName.'" name="'.$fullVarName.'" value="'.Configuration::get($fullVarName).'" /></p>';
				} else {
					$html.='<p><label for="'.$varName.'">'.$var["title"].' :</label>
					<select id="'.$fullVarName.'" name="'.$fullVarName.'">';
					foreach ($var["values"] as $value) {
						if (Configuration::get($fullVarName)==$value) {
							$html.='<option value="'.$value.'" selected="selected">'.$value.'</option>';
						} else {
							$html.='<option value="'.$value.'">'.$value.'</option>';
						}
					}
					
					$html.='</select>';					
				}
			}	
		
				
				$html.='<div class="margin-form">
					<input type="submit" name="submitModule" value="'.$this->l('Update settings').'" class="button" /></center>
				</div>
			</fieldset>
		</form>
		';
		
		return $html;
	}
	
	public function hookHeader() {
		$this->context->controller->addJS("http://widgets.twimg.com/j/2/widget.js");
		
		//$this->context->controller->addCSS(($this->_path).'blockcontactinfos.css', 'all');
	}
	
	public function hookLeftColumn($params) {
		$this->hookRightColumn($params);
	}
	
	
	public function hookRightColumn($params) {
		$height = Configuration::get('facebooklikebox_height');
		
		if (empty($height)) {
			$height= 590;
		}
		
		
		
		return '<div class="block twitter-block">
				<script charset="utf-8" src="http://widgets.twimg.com/j/2/widget.js"></script>
					<script>
					$(function() {
						new TWTR.Widget({
						  version: 2,
						  type: \'profile\',
						  rpp: 4,
						  interval: 30000,
						  width: '.Configuration::get('twitterprofilewidget_width').',
						  height: '.Configuration::get('twitterprofilewidget_height').',
						  theme: {
						    shell: {
						      background: \''.Configuration::get('twitterprofilewidget_shellbackground').'\',
						      color: \''.Configuration::get('twitterprofilewidget_shelltext').'\'
						    },
						    tweets: {
						      background: \''.Configuration::get('twitterprofilewidget_tweetbackground').'\',
						      color: \''.Configuration::get('twitterprofilewidget_tweetcolor').'\',
						      links: \''.Configuration::get('twitterprofilewidget_links').'\'
						    }
						  },
						  features: {
						    scrollbar: false,
						    loop: false,
						    live: false,
						    behavior: \'all\'
						  }
						}).render().setUser(\''.Configuration::get('twitterprofilewidget_username').'\').start();
						});
					</script>
				</div>';
	}
}
?>







