<?php
/**
 * CApplicationSetting Intermediary model
 *
 * Class that will act as nexus between CApplicationSettings Component and Active Record model
 *
 * @author Jorge Sivil <jsivil@fusiondev.com.ar>
 * @since CApplicationSettings-1.0
 */
class ApplicationSetting
{
  public static $settingsIdColumn;
  public static $settingsValueColumn;
  public static $settingsCategoryColumn;
  public static $categoryName;

  public function __construct( $config = array() )
  {
    if( $config )
    {
      self::$settingsIdColumn = $config['settingsIdColumn'];
      self::$settingsValueColumn = $config['settingsValueColumn'];
      self::$settingsCategoryColumn = $config['settingsCategoryColumn'];
      self::$categoryName = $config['categoryName'];
      new ApplicationSettingAR('insert',$config); // Initialize ApplicationSettingAR properties (table, connection, etc)
    }
  }

  /**
   * Magic getter
   *
   * @param $attr string The attribute that wants to get.
   * @return string|null
   */
  public function __get($attr)
  {
    $model = $this->getModel($attr);
    if( $model !== null )
    {
      return $model->{self::$settingsValueColumn};
    }
    if(self::$categoryName)
      throw new CHttpException('Property \'' . $attr . '\' in category \'' . self::$categoryName . '\' does not exists.', 503 );
    else
      throw new CHttpException('Property \'' . $attr . '\' does not exists.', 503 );
  }

  /**
   * Magic setter
   *
   * @param $attr string The attribute that wants to set.
   * @param $value string The value that the attribute should get saved on.
   * @throws CHttpException If value doesn't get saved correctly or it doesn't exists.
   */
  public function __set($attr, $value)
  {
    $model = $this->getModel($attr);
    if( $model !== null )
    {
      $model->{self::$settingsValueColumn} = $value;
      if( !$model->save() )
        throw new CHttpException('Could not save value \'' . $value . '\' to property \'' . $attr . '\'.', 503 );
    }
    throw new CHttpException('Could not get property \'' . $attr . '\' to assign value \'' . $value . '\'.', 503 );
  }

  /**
   * Gets CApplicationSettings' ActiveRecord model.
   *
   * @param $attr
   * @return CActiveRecord|null
   */
  private function getModel($attr)
  {
    if( self::$settingsCategoryColumn )
    {
      $model = ApplicationSettingAR::model()->find(
        self::$settingsIdColumn . ' = :propName AND ' . self::$settingsCategoryColumn . ' = :catName',
        array(':propName'=>$attr,':catName'=>self::$categoryName)
      );
    }
    else
    {
      $model = ApplicationSettingAR::model()->find(self::$settingsIdColumn . ' = :propName',array(':propName'=>$attr));
    }
    return $model;
  }
}