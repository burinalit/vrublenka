<?php
/**
 * Шаблон для модулей
 * ver. 1.0
 */
class WtGtModuleBehavior
{
    public $settings = array();
    public $settings_default = array();

    public function loadSettings($name){
        $settings = get_option($name);

        if (empty($settings)) return;

        $this->settings = array_merge($this->settings, $settings);
    }

    public function getSetting($name)
    {
        if (!empty($this->settings[$name])) $setting_value = $this->settings[$name];
        elseif (!empty($this->settings_default[$name])) $setting_value = $this->settings_default[$name];

        if (empty($setting_value)) return null;

        return $setting_value;
    }

    public function checkSetting($name){
        if (empty($this->settings[$name])) return false;
        if ($this->settings[$name] != 'on') return false;

        return true;
    }
}