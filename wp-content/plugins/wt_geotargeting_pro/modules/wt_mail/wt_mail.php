<?php

/**
 * Email-рассылка
 * User: Roman Kusty
 * Date: 03.03.2017
 */
class WtMail
{
    function __construct(){
        // Добавление регионального электронного адреса получателя
        add_filter('wp_mail', array($this, 'wpMailComponentsFilter'));
    }

    /**
     * Добавление к получателю письма электронного ящика регионального администратора
     * alias@geotargeting.wt - алиас, при наличии которого активируется отправка
     *
     * @param $components
     * @return mixed
     */
    public function wpMailComponentsFilter($components){
        /* Проверяем наличие алиаса "alias@geotargeting.wt" в списке получателей */
        $email_to = $this->stringToArray($components['to']);
        $check_email_alias = array_search('alias@geotargeting.wt', $email_to);
        if (FALSE === $check_email_alias) return $components;

        /* Удаляем из списка получателей алиас */
        unset($email_to[$check_email_alias]);
        $components['to'] = implode(', ' , $email_to);

        $region_admin_email = Wt::$obj->contacts->getValue('admin_email');

        if (empty($region_admin_email)) return $components;
        $region_admin_emails = $this->stringToArray($region_admin_email);


        $email_to = array_merge($email_to, $region_admin_emails);

        $components['to'] = implode(', ' , $email_to);

        return $components;
    }

    function stringToArray($string){
        $values = explode(",", $string);

        foreach ($values as $key => $value){
            $values[$key] = trim($value);
        }
        return $values;
    }
}