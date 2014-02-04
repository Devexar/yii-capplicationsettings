<?php

/**
 * CApplicationSetting Component
 *
 * This component is the one that manages applications and serves models.
 *
 * Example configuration:
 *
 * protected/config/main.php
 *
 * components=>array(
 * ...
 *   'settings'=>array(
 *     'class'=>'CApplicationSettings',
 *     'settingsTable'=>'{{setting}}',
 *     'settingsIdColumn'=>'name',
 *     'applications'=>array(
 *       'main'=>array(
 *         'settingsTable'=>'{{module_setting}}',
 *         'settingsIdColumn'=>'name',
 *         'settingsCategoryColumn'=>'module',
 *        ),
 *       'forum'=>array(
 *         'isCategoryOf'=>'main' //Inherits all the config from 'main' application.
 *       ),
 *       'blog'=>array(
 *         'isCategoryOf'=>'main' //Inherits all the config from 'main' application.
 *       ),
 *      'anotherForum'=>array(
 *         'settingsTable'=>'forum_settings',
 *         'settingsIdColumn'=>'setting_name',
 *         'settingsValueColumn'=>'setting_value',
 *         'connection'=>'forum_db_conn',
 *       ),
 *     ),
 *   ),
 *...
 * ),
 *
 * This way you can access forum_home_url these ways:
 *
 * Yii::app()->settings->forum->forum_home_url  // Condition will be 'name = `forum_home_url` AND module = `forum`'
 * Yii::app()->settings->main->forum_home_url  // Condition will be 'name = `forum_home_url`'
 * Yii::app()->settings->blog->forum_home_url  // Will throw Exception because doesn't exists in condition 'module = `blog`'
 *
 * And anotherForum is another application.
 *
 * Yii::app()->settings->anotherForum->someSetting
 * Yii::app()->settings->anotherForum->someSetting = value
 *
 *
 * Report any bug at http://tracker.fusiondev.com.ar
 *
 * @author Jorge Sivil <jsivil@fusiondev.com.ar>
 * @since CApplicationSettings-1.0
 */
class CApplicationSettings extends CApplicationComponent
{
  public $cache = false;
  public $connection = 'db';
  public $settingsTable = 'settings';
  public $settingsIdColumn = 'id';
  public $settingsValueColumn = 'value';
  public $applications = array();
  public $settingsCategoryColumn = null;
  /**
   * @var array Array models are saved to avoid multiple instantiation of the same model.
   */
  private static $_models = array();

  /**
   * Magic setter that acts as application's name.
   *
   * If it exists, returns a intermediary model between
   * this Component and the Active Record class.
   *
   * @param string $app
   * @return null|ApplicationSetting
   */
  public function __get( $app )
  {
    if( $this->applications && array_key_exists($app, $this->applications) )
    {
      self::$_models[$app] = new ApplicationSetting($this->makeConfig($app));
      return self::$_models[$app];
    }
    else
    {
      return parent::__get( $app );
    }
  }

  /**
   * Makes config array.
   *
   * Returns a config array that will be used for CApplicationSetting model instantiation.
   *
   * @param $app
   * @return array
   */
  private function makeConfig($app)
  {
    $settingsCategoryColumn = isset( $this->applications[$app]['settingsCategoryColumn'] )
      ? $this->applications[$app]['settingsCategoryColumn']
      : $this->settingsCategoryColumn;

    if(isset($this->applications[$app]['isCategoryOf']))
    {
      $category = $app;
      $app = $this->applications[$app]['isCategoryOf'];
    }
    else
      $settingsCategoryColumn = '';
    // To allow parent app still access all the values, other way a condition comparing category will be raised.

    $connection = isset( $this->applications[$app]['connection'] )
      ? $this->applications[$app]['connection']
      : $this->connection;

    $settingsTable = isset( $this->applications[$app]['settingsTable'] )
      ? $this->applications[$app]['settingsTable']
      : $this->settingsTable;

    $settingsIdColumn = isset( $this->applications[$app]['settingsIdColumn'] )
      ? $this->applications[$app]['settingsIdColumn']
      : $this->settingsIdColumn;

    $settingsValueColumn = isset( $this->applications[$app]['settingsValueColumn'] )
      ? $this->applications[$app]['settingsValueColumn']
      : $this->settingsValueColumn;


    return $config = array(
      'connection'=>$connection,
      'settingsTable'=>$settingsTable,
      'settingsIdColumn'=>$settingsIdColumn,
      'settingsValueColumn'=>$settingsValueColumn,
      'settingsCategoryColumn'=>$settingsCategoryColumn,
      'categoryName'=>isset($category) ? $category : null,
    );
  }
}