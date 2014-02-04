<?php
/**
 * CApplicationSetting Active Record
 *
 * Class that will act as nexus between CApplicationSettings and the DB.
 *
 * @author Jorge Sivil <jsivil@fusiondev.com.ar>
 * @since CApplicationSettings-1.0
 */
class ApplicationSettingAR extends CActiveRecord
{
  public static $settingsTable;
  public static $settingsIdColumn;
  public static $settingsValueColumn;

  public static function model($className=__CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return self::$settingsTable;
  }

  public function __construct( $scenario='insert', $config = array() )
  {
    if( $config )
    {
      self::$db = Yii::app()->$config['connection'];
      self::$settingsTable = $config['settingsTable'];
      self::$settingsIdColumn = $config['settingsIdColumn'];
      self::$settingsValueColumn = $config['settingsValueColumn'];
    }
    parent::__construct($scenario);
  }
}
